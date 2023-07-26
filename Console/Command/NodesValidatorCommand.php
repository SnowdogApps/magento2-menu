<?php

declare(strict_types=1);

namespace Snowdog\Menu\Console\Command;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\LocalizedException;
use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Api\MenuRepositoryInterface;
use Snowdog\Menu\Api\NodeRepositoryInterface;
use Snowdog\Menu\Model\ImportExport\Processor\Import\Node\Validator;
use Snowdog\Menu\Model\ImportExport\Processor\Import\Node\Validator\TreeTrace;
use Snowdog\Menu\Model\ImportExport\Processor\Import\Validator\ValidationAggregateError;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class NodesValidatorCommand extends Command
{
    private MenuRepositoryInterface $menuRepository;
    private NodeRepositoryInterface $nodeRepository;
    private SearchCriteriaBuilder $searchCriteriaBuilder;
    private Validator $validator;
    private ValidationAggregateError $validationAggregateError;
    private State $state;
    private TreeTrace $treeTrace;

    public function __construct(
        MenuRepositoryInterface $menuRepository,
        NodeRepositoryInterface $nodeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Validator $validator,
        ValidationAggregateError $validationAggregateError,
        State $state,
        TreeTrace $treeTrace,
        string $name = null
    ) {
        $this->menuRepository = $menuRepository;
        $this->nodeRepository = $nodeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->validator = $validator;
        $this->validationAggregateError = $validationAggregateError;
        $this->state = $state;
        $this->treeTrace = $treeTrace;

        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $description = 'It will loop all menu nodes and return the IDs and description of invalid menu nodes.'
            . PHP_EOL
            . ' Additionally, it will ask to remove the detected invalid node if needed.';

        $this->setName('snowdog-menu:validate-nodes')
            ->setDescription($description);

        parent::configure();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setAreaCode();
        $invalidNodeIds = [];
        $this->treeTrace->disableNodeIdAddend();

        foreach ($this->getAllMenus() as $menu) {
            $output->writeln(PHP_EOL . '<info>... Validating nodes for menu with ID: ' . $menu->getMenuId(). ' </info>');
            $nodes = $this->nodeRepository->getByMenu($menu->getMenuId());
            $menuInvalidNodes = $this->getInvalidNodes($nodes);

            if (empty($menuInvalidNodes)) {
                $output->writeln('<info>No invalid nodes encountered</info>');
                continue;
            }

            $output->writeln(
                '<error>Invalid nodes encountered for menu ID: ' . $menu->getMenuId() . '</error>'
            );

            foreach ($menuInvalidNodes as $errorMessages) {
                foreach ($errorMessages as $errorMessage) {
                    $output->writeln("<error>- $errorMessage</error>");
                }
            }

            $invalidNodeIds[] = array_keys($menuInvalidNodes);
        }

        if (empty($invalidNodeIds) || !$input->isInteractive()) {
            return 0;
        }

        $invalidNodeIds = array_merge([], ...$invalidNodeIds);
        $helper = $this->getHelper('question');

        if (!$helper->ask($input, $output, $this->getConfirmationQuestion())) {
            $output->writeln(PHP_EOL . '<info>Invalid nodes were NOT removed</info>');
            return 0;
        }

        $this->displayResults(
            $this->deleteNodesByIds($invalidNodeIds),
            $output
        );

        return 0;
    }

    private function setAreaCode(): void
    {
        try {
            $this->state->getAreaCode();
        } catch (LocalizedException $e) {
            $this->state->setAreaCode(Area::AREA_ADMINHTML);
        }
    }

    /**
     * @return MenuInterface[]
     */
    private function getAllMenus(): array
    {
        return $this->menuRepository
            ->getList($this->searchCriteriaBuilder->create())
            ->getItems();
    }

    private function getInvalidNodes(array $nodes): array
    {
        $invalidNodes = [];

        /** @var NodeInterface $node */
        foreach ($nodes as $node) {
            $this->validator->validate(
                [$node->getNodeId() => $node->getData()]
            );

            if (empty($this->validationAggregateError->getErrors())) {
                continue;
            }

            foreach ($this->validationAggregateError->getErrorMessages() as $errorMessage) {
                $invalidNodes[$node->getNodeId()][] = $errorMessage;
            }

            $this->validationAggregateError->flush();
        }

        return $invalidNodes;
    }

    private function deleteNodesByIds(array $nodeIds): array
    {
        $errors = [];
        $success = [];

        foreach ($nodeIds as $nodeId) {
            try {
                $this->nodeRepository->deleteById($nodeId);
                $success[] = $nodeId;
            } catch (CouldNotDeleteException $e) {
                $errors[] = sprintf(
                    'Could not remove node with ID %s, reason: %s',
                    $nodeId,
                    $e->getMessage()
                );
            }
        }

        return ['success' => $success, 'errors' => $errors];
    }

    private function getConfirmationQuestion(): ConfirmationQuestion
    {
        return new ConfirmationQuestion(
            PHP_EOL . ' > Would you like to remove the invalid nodes listed above? (y/n): ',
            false
        );
    }

    private function displayResults(array $results, OutputInterface $output): void
    {
        foreach ($results['errors'] as $error) {
            $output->writeln('<error>' . $error . '</error>');
        }

        if (!empty($results['success'])) {
            $output->writeln(
                sprintf(
                    PHP_EOL . '<info>The following invalid nodes were removed successfully: %s</info>',
                    implode(', ', $results['success'])
                )
            );
        }
    }
}
