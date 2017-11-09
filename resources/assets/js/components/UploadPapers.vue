<template>
    <div class="container">
        <div class="row main">
            <div class="main-center">
                <h1>Upload Document</h1>
                <form class="" method="post" action="#">

                    <div class="form-group">
                        <label for="name" class="cols-sm-2 control-label">File Name</label>
                        <div class="cols-sm-10">
                            <div class="input-group">
                                <input type="text" class="form-control" name="name" id="name"
                                       placeholder="Enter File Name"/>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="resource" class="cols-sm-2 control-label">Resource Type</label>
                        <div class="cols-sm-10">
                            <select class="form-control" id="resource">
                                <option selected>Choose Resource type</option>
                                <option>Exam</option>
                                <option>CAT</option>
                                <option>Assignment</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="semester" class="cols-sm-2 control-label">Semester</label>
                        <div>
                            <div class="input-group">
                                <input type="text" class="form-control input-normal" name="username" id="semester"
                                       placeholder="Enter Semester"/>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="cols-sm-10">
                            <div class="form-file">
                                <input type="file" id="file" class="file-input">
                                <span class="form-file-control"></span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0"
                                     aria-valuemax="100" style="width: 60%;">
                                    60%
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group ">
                        <a href="http://deepak646.blogspot.in" target="_blank" type="button" id="button"
                           class="btn btn-primary btn-lg btn-block">Upload</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</template>

<script>

</script>

<style>
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
