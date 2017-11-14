<template>
    <div class="container">
        <div class="row main">
            <div class="main-center">
                <h1>Upload Document</h1>
                <form class="">

                    <div class="form-group">
                        <label for="name" class="control-label">File Name</label>
                        <div class="input-group">
                            <input type="text" v-model="docInfo.name" class="form-control" name="name" id="name"
                                   placeholder="Enter File Name"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="resource" class="control-label">Resource Type</label>
                        <select class="form-control" v-model="docInfo.resource_type" id="resource">
                            <option selected>Choose Resource type</option>
                            <option>Exam</option>
                            <option>CAT</option>
                            <option>Assignment</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="semester" class="control-label">Semester</label>
                        <div>
                            <div class="input-group">
                                <input type="text" class="form-control" v-model="docInfo.semester" name="username"
                                       id="semester"
                                       placeholder="Enter Semester"/>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-file">
                            <input type="file" id="file" @change="onFileChange" class="file-input"
                                   accept="application/pdf">
                        </div>
                    </div>

                    <div class="progress">
                        <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0"
                             aria-valuemax="100" style="width: 60%;">
                            60%
                        </div>
                    </div>

                    <div class="form-group ">
                        <a href="#" v-on:click="postDoc" type="button" id="button"
                           class="btn btn-primary btn-lg btn-block">Upload</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</template>

<script>
    import {uploadPastPaper} from '../file-upload.service';
    import axios from "axios";

    export default {
        name: 'app',
        data() {
            return {
                docInfo: {
                    name: '',
                    resource_type: '',
                    semester: ''
                },
                doc: null
            }
        },
        methods: {
            onFileChange(e) {
                let files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.createFile(files[0]);
            },
            postDoc: function () {
                let data = this.docInfo;

                var formdata = new FormData(this);
                formdata.append("name", data.name);
                formdata.append("resource_type", data.resource_type);
                formdata.append("semester", data.semester);
                formdata.append("file", this.doc);

                uploadPastPaper(formdata).then(x => {
                    console.log(x);
                }).catch(err => {
                    console.log(err);
                });
            },
            createFile(file) {
                let reader = new FileReader();
                let vm = this;
                reader.onload = (e) => {
                    vm.doc = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    }
</script>

<style lang="scss">
    body, html {
        font-family: 'Oxygen', sans-serif;
    }

    .main {
        margin: 50px 15px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    input {
        margin: auto;
        width: 100%;
    }

    #button {
        border: 1px solid #ccc;
        margin-top: 28px;
        padding: 6px 6px;
        cursor: pointer;
        -moz-border-radius: 3px 3px;
        -webkit-border-radius: 3px 3px;
        border-radius: 3px 3px;
        -moz-box-shadow: 0 1px #fff inset, 0 1px #ddd;
        -webkit-box-shadow: 0 1px #fff inset, 0 1px #ddd;
        box-shadow: 0 1px #fff inset, 0 1px #ddd;
    }

    .main-center {

        margin: 0 auto;
        max-width: 400px;
        padding: 10px 40px;
        text-shadow: none;
        -webkit-box-shadow: 0px 3px 5px 0px rgba(0, 0, 0, 0.31);
        -moz-box-shadow: 0px 3px 5px 0px rgba(0, 0, 0, 0.31);
        box-shadow: 0px 3px 5px 0px rgba(0, 0, 0, 0.31);

    }
</style>
