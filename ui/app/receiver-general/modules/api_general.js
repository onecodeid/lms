import { URL, ajaxPost } from "../../assets/js/global.js"

export async function search(prm) {
    return ajaxPost(URL + 'ajax/listing_general.php', prm)
}

export async function search_province(prm) {
    return ajaxPost(URL + 'ajax/listing_province.php', prm)
}

export async function search_city(prm) {
    return ajaxPost(URL + 'ajax/listing_city.php', prm)
}