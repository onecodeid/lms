import { URL, ajaxPost } from "../../assets/js/global.js"

export async function search(prm) {
    return ajaxPost(URL + 'inventory/opname/search', prm)
}

export async function search_detail(prm) {
    return ajaxPost(URL + 'inventory/opnamedetail/search', prm)
}

export async function search_item(prm) {
    return ajaxPost(URL + 'inventory/opnamedetail/search_item', prm)
}

export async function save(prm) {
    return ajaxPost(URL + 'inventory/opname/save', prm)
}