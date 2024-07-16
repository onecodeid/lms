import { URL, ajaxPost } from "../../assets/js/global.js"

export async function search(prm) {
    return ajaxPost(URL + 'finance/targetomzet/search', prm)
}

export async function save(prm) {
    return ajaxPost(URL + 'finance/targetomzet/save', prm)
}
