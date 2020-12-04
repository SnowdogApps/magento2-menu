<template>
    <div class="admin__field field field-title upload-file">
        <label
            class="label admin__field-label"
            :for="fieldId"
        >
            {{ labels.field }}
        </label>

        <div class="admin__field-control control image-upload__wrapper">
            <div
                v-if="itemImageUrlPreview"
                class="upload-file__current-area"
            >
                <img
                    class="upload-file__image image-upload__image--current"
                    :src="itemImageUrlPreview"
                >
            </div>
            <button
                v-if="itemImageUrlPreview"
                class="primary image-upload__remove"
                @click="removeItemImage"
            >
                {{ labels.removeAction }}
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
                            {{ labels.cancelAction }}
                        </button>
                        <button
                            class="primary"
                            @click="saveFile"
                        >
                            {{ labels.saveAction }}
                        </button>
                    </div>
                </div>
                <div
                    v-if="!previewImage && !fileIsUploading"
                    class="image-upload__upload-area"
                >
                    <button
                        @click="chooseFile"
                    >
                        {{ labels.uploadAction }}
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
                    {{ fileIsUploadingLabel }}
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
            props: {
                labels: {
                    type: Object,
                    required: true
                },
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
                    itemImageUrlPreview: this.item.imageUrl,
                    fileIsUploading: false,
                    fileIsUploadingLabel: $t('Uploading file ...'),
                    uploadError: ''
                }
            },
            mounted: function() {
                this.fieldId = 'snowmenu_' + this.id + '_' + this._uid;
            },
            methods: {
                setItemImage: function(file, url) {
                  this.item.image = file;
                  this.item.imageUrl = url;
                  this.itemImageUrlPreview = url;
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
                        success: function (response) {
                          if (response.file) {
                            this.setItemImage(response.file, response.url);
                          }
                        }.bind(this),
                        error: function() {
                           this.uploadError = $t('There was an error during uploading. Please try again.')
                        }.bind(this),
                        complete: function() {
                          this.fileIsUploading = false;
                          this.removeNewFile();
                        }.bind(this)
                    });
                },

                removeItemImage: function(e) {
                  e.preventDefault();
                  this.setItemImage('', '');
                },

                removeNewFileAction: function(e) {
                    e.preventDefault();
                    this.removeNewFile();
                },

                removeNewFile: function() {
                    this.$refs.fileUpload.value = '';
                    this.previewImage = '';
                },

                assignImage: function(e) {
                    this.previewImage = e.target.result;
                },

                saveFile: function() {
                    this.uploadFileToServer();
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
