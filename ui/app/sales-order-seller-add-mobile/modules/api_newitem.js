import { URL, ajaxPost } from "../../assets/js/global.js"

export async function search_item(prm) {
    return ajaxPost(URL + 'master/item/search_w_price', prm)
}

export async function search_packet(prm) {
    return ajaxPost(URL + 'master/packet/search_w_price', prm)
}