import { URL, ajaxPost } from "../../assets/js/global.js"

export async function search(prm) {
    return ajaxPost(URL + 'finance/payment/search', prm)
}

// export async function search_item(prm) {
//     return ajaxPost(URL + 'sales/sodetail/search', prm)
// }

// export async function search_expedition(prm) {
//     return ajaxPost(URL + 'master/expedition/search', prm)
// }

// export async function search_service(prm) {
//     return ajaxPost(URL + 'master/expedition/search_service', prm)
// }

export async function confirm(prm) {
    return ajaxPost(URL + 'finance/payment/confirm', prm)
}