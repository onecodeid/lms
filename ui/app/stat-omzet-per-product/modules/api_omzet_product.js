import { URL, ajaxPost } from "../../assets/js/global.js"

export async function search(prm) {
    return ajaxPost(URL + 'dashboard/dashboard/get_omzet_total_by_product', prm)
}

export async function search_area(prm) {
    return ajaxPost(URL + 'report/stat/stat_sales_005', prm)
}

export async function search_qty(prm) {
    return ajaxPost(URL + 'report/stat/Stat_sales_001', prm)
}
