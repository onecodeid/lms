import { URL, ajaxPost } from "../../assets/js/global.js"

export async function search(prm) {
    return ajaxPost(URL + 'master/reward/search', prm)
}

export async function save(prm) {
    return ajaxPost(URL + 'master/reward/save', prm)
}

export async function del(prm) {
    return ajaxPost(URL + 'master/reward/del', prm)
}

export async function search_customer_single(prm) {
    return ajaxPost(URL + 'master/customer/search_single', prm)
}

export async function search_customer(prm) {
    return ajaxPost(URL + 'master/customer/search', prm)
}

export async function redeem_reward(prm) {
    return ajaxPost(URL + 'finance/point/redeem_reward', prm)
}

export async function search_redeem(prm) {
    return ajaxPost(URL + 'finance/redeem/search', prm)
}

export async function redeem_cancel(prm) {
    return ajaxPost(URL + 'finance/redeem/redeem_cancel', prm)
}

export async function redeem_send(prm) {
    return ajaxPost(URL + 'finance/redeem/redeem_send', prm)
}

// export async function search_unit(prm) {
//     return ajaxPost(URL + 'master/unit/search', prm)
// }

// export async function search_level_price(prm) {
//     return ajaxPost(URL + 'master/price/search_by_reward', prm)
// }