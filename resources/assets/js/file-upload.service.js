import * as axios from "axios";

function upload(formData) {
    const url = '/api/v1/files/db';
    return axios.post(url, formData);
}

function uploadPastPaper(formData) {
    const url = '/api/v2/papers';
    const config = {headers: {'Accept': 'application/json'}};
    console.log(formData.get('file'));
    return axios.post(/*'http://httpbin.org/post'*/url, formData, config);
}

export {upload, uploadPastPaper}