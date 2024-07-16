import { URL, ajaxPost } from "../../assets/js/global.js"

export async function search(prm) {
    return ajaxPost(URL + 'warehouse/courier/search', prm)
}

// export async function search_item(prm) {
//     return ajaxPost(URL + 'sales/sodetail/search', prm)
// }

export async function search_expedition(prm) {
    return ajaxPost(URL + 'master/expedition/search', prm)
}

// export async function search_service(prm) {
//     return ajaxPost(URL + 'master/expedition/search_service', prm)
// }

export async function process(prm) {
    return ajaxPost(URL + 'warehouse/courier/process', prm)
}

export async function send(prm) {
    return ajaxPost(URL + 'warehouse/courier/send', prm)
}

export async function receipt(prm) {
    return ajaxPost(URL + 'warehouse/courier/receipt', prm)
}

export async function waybill(prm) {
    return ajaxPost(URL + 'master/expedition/waybill', prm)
}