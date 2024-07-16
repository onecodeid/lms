import { URL, ajaxPost } from "../../assets/js/global.js"

// export async function search(prm) {
//     return ajaxPost(URL + 'dashboard/dashboard/get_omzet_total_by_product', prm)
// }

export async function search(prm) {
    return ajaxPost(URL + 'report/stat/Stat_sales_003', prm)
}

export async function search_pareto(prm) {
    return ajaxPost(URL + 'report/stat/Stat_pareto_per_cities', prm)
}