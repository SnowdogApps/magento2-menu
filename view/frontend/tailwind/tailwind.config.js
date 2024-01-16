module.exports = {
    content: [
        '../templates/**/*.phtml',
    ],
    safelist: ['min-w-full'],
    theme: {
        extend: {
            maxWidth: {
                52: '13rem',
            }
        }
    }
}
