import { URL, ajaxPost } from "../../assets/js/global.js"

export async function search(prm) {
    return ajaxPost(URL + 'warehouse/courier/search', prm)
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

export async function process(prm) {
    return ajaxPost(URL + 'warehouse/courier/process', prm)
}