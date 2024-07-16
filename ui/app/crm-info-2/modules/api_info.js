import { URL, ajaxPost } from "../../assets/js/global.js"

export async function search(prm) {
    return ajaxPost(URL + 'crm/info/search', prm)
}

export async function save(prm) {
    return ajaxPost(URL + 'notice/message/save', prm)
}

export async function del(prm) {
    return ajaxPost(URL + 'crm/info/del', prm)
}

// export async function search_unit(prm) {
//     return ajaxPost(URL + 'crm/unit/search', prm)
// }

// export async function search_level_price(prm) {
//     return ajaxPost(URL + 'crm/price/search_by_watemplate', prm)
// }