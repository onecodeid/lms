import { URL, ajaxPost } from "../../assets/js/global.js"

export async function search(prm) {
    return ajaxPost(URL + 'purchase/purchase/search', prm)
}

export async function search_item(prm) {
    return ajaxPost(URL + 'purchase/purchasedetail/search', prm)
}

export async function search_vendor(prm) {
    return ajaxPost(URL + 'master/vendor/search', prm)
}

export async function search_items(prm) {
    return ajaxPost(URL + 'master/item/search', prm)
}

export async function save(prm) {
    return ajaxPost(URL + 'purchase/purchase/save', prm)
}
// export async function approve(prm) {
//     return ajaxPost(URL + 'purchase/purchase/approve', prm)
// }