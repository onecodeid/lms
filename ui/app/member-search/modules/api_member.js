import { ajaxPost } from "../app/assets/js/global.js"

export async function search_member(prm) {
    prm.crossDomain = true
    return ajaxPost('https://register.zalfa.id/get_member.php', prm)
}