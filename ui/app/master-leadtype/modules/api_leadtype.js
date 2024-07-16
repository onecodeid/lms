import { URL, ajaxPost } from "../../assets/js/global.js"

export async function search(prm) {
    return ajaxPost(URL + 'master/leadtype/search', prm)
}

export async function save(prm) {
    return ajaxPost(URL + 'master/leadtype/save', prm)
}

export async function del(prm) {
    return ajaxPost(URL + 'master/leadtype/del', prm)
}

// export async function search_unit(prm) {
//     return ajaxPost(URL + 'master/unit/search', prm)
// }

// export async function search_level_price(prm) {
//     return ajaxPost(URL + 'master/price/search_by_leadtype', prm)
// }