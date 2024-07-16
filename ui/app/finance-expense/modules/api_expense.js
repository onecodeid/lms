import { URL, ajaxPost } from "../../assets/js/global.js"

export async function search(prm) {
    return ajaxPost(URL + 'finance/expense/search', prm)
}

export async function save(prm) {
    return ajaxPost(URL + 'finance/expense/save', prm)
}

export async function del(prm) {
    return ajaxPost(URL + 'finance/expense/del', prm)
}

export async function search_expense(prm) {
    return ajaxPost(URL + 'master/expense/search', prm)
}

export async function save_m_expense(prm) {
    return ajaxPost(URL + 'master/expense/save', prm)
}
// export async function search_level_price(prm) {
//     return ajaxPost(URL + 'master/price/search_by_item', prm)
// }