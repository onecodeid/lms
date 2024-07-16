import { URL, ajaxPost } from "../../assets/js/global.js"

export async function search(prm) {
    return ajaxPost(URL + 'finance/Omzet_pivot/search', prm)
}

export async function confirm(prm) {
    return ajaxPost(URL + 'finance/payment/confirm', prm)
}

export async function degrade(prm) {
    return ajaxPost(URL + 'master/customer/degrade', prm)
}

export async function search_level(prm) {
    return ajaxPost(URL + 'master/customerlevel/search', prm)
}