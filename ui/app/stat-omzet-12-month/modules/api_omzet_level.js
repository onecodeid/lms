import { URL, ajaxPost } from "../../assets/js/global.js"

// export async function search(prm) {
//     return ajaxPost(URL + 'dashboard/dashboard/get_omzet_total_by_product', prm)
// }

export async function search(prm) {
    return ajaxPost(URL + 'report/stat/Stat_sales_002', prm)
}

export async function search_level(prm) {
    return ajaxPost(URL + 'master/customerlevel/search', prm)
}

export async function search_user(prm) {
    return ajaxPost(URL + 'systm/user/search', prm)
}