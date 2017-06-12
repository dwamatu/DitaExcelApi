import * as axios from "axios";

function upload(formData) {
    const url = `${URL}/api/v1/files`;
    return axios.post(url, formData)
        .then(x => x.data)
        .then(x => {
            x.url = `${URL}/file/${x.type.name}`;
            return x;
        });
}

export {upload}