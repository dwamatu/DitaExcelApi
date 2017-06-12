import * as axios from "axios";

const BASE_URL = 'http://localhost:8000';

function upload(formData) {
    const url = `${BASE_URL}/api/v1/files`;
    return axios.post(url, formData)
        .then(x => x.data)
        .then(x => {
            x.url = `${BASE_URL}/file/${x.type.name}`;
            return x;
        });
}

export {upload}