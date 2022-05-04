<template>
    <div class="admin__field field field-title upload-file">
        <label
            class="label admin__field-label"
            :for="fieldId"
        >
            {{ labelField }}
        </label>

        <div class="admin__field-control control image-upload__wrapper">
            <div
                v-if="item.image_url"
                class="upload-file__current-area"
            >
                <img
                    class="upload-file__image image-upload__image--current"
                    :src="item.image_url"
                >
            </div>
            <button
                v-if="item.image_url"
                class="primary image-upload__remove"
                @click="removeItemImage"
            >
                {{ labelRemoveAction }}
            </button>
            <div
                class="image-upload__dropzone"
                :class="{'image-upload__dropzone--dragging': isDragging}"
                @dragenter="onDragEnter"
                @dragleave="onDragLeave"
                @dragover.prevent
                @drop="onDrop"
            >
                <input
                    :id="fieldId"
                    ref="fileUpload"
                    type="file"
                    class="input-text admin__control-text hidden"
                    :name="fieldId"
                    @change="updateFile"
                >
                <div
                    v-if="previewImage && !fileIsUploading"
                    class="image-upload__image-area"
                >
                    <img
                        class="image-upload__image"
                        :src="previewImage"
                    >
                    <div class="image-upload__dropzone-actions">
                        <button
                            class="secondary"
                            @click="removeNewFile"
                        >
                            {{ labelCancelAction }}
                        </button>
                        <button
                            class="primary"
                            @click="uploadFileToServer"
                        >
                            {{ labelSaveAction }}
                        </button>
                    </div>
                </div>
                <div
                    v-if="!previewImage && !fileIsUploading"
                    class="image-upload__upload-area"
                >
                    <button @click="chooseFile">
                        {{ item.image_url ? labelChangeAction : labelUploadAction }}
                    </button>
                    <div
                        v-if="uploadError"
                        class="image-upload__errors mage-error"
                    >
                        {{ uploadError }}
                    </div>
                </div>
                <div
                    v-if="fileIsUploading"
                    class="fileIsUploading"
                >
                    {{ labelFileIsUploading }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    define([
        'Vue',
        'Magento_Ui/js/modal/alert',
        'jquery',
        'mage/translate'
    ], function(Vue, alert, $, $t) {
        'use strict';

        Vue.component('image-upload', {
            name: 'image-upload',
            props: {
                id: {
                    type: String,
                    required: true
                },
                item: {
                    type: Object,
                    required: true
                }
            },
            data: function() {
                return {
                    isDragging: false,
                    fieldId: '',
                    selectedFile: '',
                    file: '',
                    previewImage: '',
                    fileIsUploading: false,

                    uploadError: '',
                    labelField: $t('Image'),
                    labelUploadAction: $t('Choose image'),
                    labelChangeAction: $t('Change image'),
                    labelCancelAction: $t('Cancel'),
                    labelRemoveAction: $t('Remove'),
                    labelSaveAction: $t('Save'),
                    labelFileIsUploading: $t('Uploading file ...'),
                }
            },
            mounted: function() {
                this.fieldId = 'snowmenu_' + this.id + '_' + this._uid;
            },
            methods: {
                setItemImage: function(file, url) {
                    this.item.image = file;
                    this.item.image_url = url;
                },
                uploadFileToServer: function() {
                    this.fileIsUploading = true;

                    var formData = new FormData();
                    formData.append(this.$root.config.imageUploadFileId, this.file);
                    formData.append('current_image', this.item.image || '');
                    formData.append('node_id', this.item.is_stored ? this.item.id : '');
                    formData.append('form_key', window.FORM_KEY);

                    $.ajax({
                        url: this.$root.config.imageUploadUrl,
                        data: formData,
                        type: 'POST',
                        contentType: false,
                        processData: false,
                        beforeSend: function () {
                            $('body').trigger('processStart');
                        },
                        success: function (response) {
                            if (response.file) {
                                this.setItemImage(response.file, response.url);
                            }
                        }.bind(this),
                        error: function() {
                            $('body').trigger('processStop');
                            this.uploadError = $t('An error has occurred during the menu node image upload.')
                        }.bind(this),
                        complete: function() {
                            this.fileIsUploading = false;
                            this.removeNewFile();
                            $('body').trigger('processStop');
                        }.bind(this)
                    });
                },

                removeItemImage: function(e) {
                    e.preventDefault();

                    if (!this.item.image) {
                        return;
                    }

                    const formData = new FormData();

                    formData.append('image', this.item.image);
                    formData.append('node_id', this.item.is_stored ? this.item.id : '');
                    formData.append('form_key', window.FORM_KEY);

                    $.ajax({
                        url: this.$root.config.imageDeleteUrl,
                        data: formData,
                        type: 'POST',
                        contentType: false,
                        processData: false,
                        beforeSend: function () {
                            $('body').trigger('processStart');
                        },
                        error: function() {
                            $('body').trigger('processStop');
                            alert({ content: $t('An error has occurred while removing the menu node image.') });
                        }.bind(this),
                        complete: function() {
                            this.setItemImage('', '');
                            $('body').trigger('processStop');
                        }.bind(this)
                    });
                },

                removeNewFile: function() {
                    this.$refs.fileUpload.value = '';
                    this.previewImage = '';
                },

                assignImage: function(e) {
                    this.previewImage = e.target.result;
                },

                previewUploadImage: function(file) {
                    this.file = file;

                    if (file.type && file.type.indexOf('image') === -1) {
                        alert({
                            content: $t('File is not an image. You tried to upload file that has type: ') + file.type
                        });
                        return;
                    }

                    var reader = new FileReader();
                    reader.addEventListener('load', this.assignImage.bind(this), false);
                    reader.readAsDataURL(file);
                },

                chooseFile: function(e) {
                    e.preventDefault();
                    this.$refs.fileUpload.click()
                },

                onDragEnter: function(e) {
                    e.preventDefault();
                    this.isDragging = true;
                },

                onDragLeave: function(e) {
                    e.preventDefault();
                    this.isDragging = false;
                },

                onDrop: function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.previewUploadImage(e.dataTransfer.files[0])
                },

                updateFile: function(event){
                    this.uploadError = '';
                    this.previewUploadImage(event.target.files[0]);
                    event.preventDefault();
                }
            },
            template: template
        });
    });
</script>
