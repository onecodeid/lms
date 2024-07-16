import { URL, ajaxPost } from "../../assets/js/global.js"

export async function uf(prm) {
    return ajaxPost(URL + 'z/z/uf', prm, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    })
}

export async function yt_api(prm) {
    return ajaxPost(URL + 'z/z/yt_api', prm)
}

export async function save(prm) {
    return ajaxPost(URL + 'z/z/save', prm)
}

export async function search(prm) {
    return ajaxPost(URL + 'z/z/search', prm)
}