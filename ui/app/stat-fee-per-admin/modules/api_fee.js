import { URL, ajaxPost } from "../../assets/js/global.js"

export async function search(prm) {
    return ajaxPost(URL + 'finance/fee/search', prm)
}

export async function search_user(prm) {
    return ajaxPost(URL + 'finance/fee/search_admin', prm)
}
// export async function search_unit(prm) {
//     return ajaxPost(URL + 'master/unit/search', prm)
// }

// export async function search_level_price(prm) {
//     return ajaxPost(URL + 'master/price/search_by_expense', prm)
// }