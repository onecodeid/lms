import { URL, ajaxPost } from "../../assets/js/global.js"

export async function search(prm) {
    return ajaxPost(URL + 'finance/cod/search', prm)
}

export async function search_pending(prm) {
    return ajaxPost(URL + 'finance/cod/search_pending', prm)
}

export async function search_item(prm) {
    return ajaxPost(URL + 'finance/coddetail/search', prm)
}

export async function search_expedition(prm) {
    return ajaxPost(URL + 'master/expedition/search', prm)
}

// export async function search_service(prm) {
//     return ajaxPost(URL + 'master/expedition/search_service', prm)
// }

// export async function confirm(prm) {
//     return ajaxPost(URL + 'finance/payment/confirm', prm)
// }

// // PAYMENT
// export async function search_bank(prm) {
//     return ajaxPost(URL + 'master/bank/search', prm)
// }

// export async function search_account(prm) {
//     return ajaxPost(URL + 'master/bankaccount/search', prm)
// }

// export async function search_order(prm) {
//     return ajaxPost(URL + 'sales/so/search_approved', prm)
// }

export async function accept(prm) {
    return ajaxPost(URL + 'finance/cod/accept', prm)
}

export async function reject(prm) {
    return ajaxPost(URL + 'finance/cod/reject', prm)
}