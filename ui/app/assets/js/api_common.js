import { URL, ajaxPost } from "./global.min.js"

export async function postme(aurl, prm, hdr) {
    if (aurl.indexOf("http") < 0) aurl = URL + aurl
    if (!hdr) return ajaxPost(aurl, prm)
    return ajaxPost(aurl, prm, hdr)
}

// export async function postme(aurl, prm) {
//     return ajaxPost(URL + aurl, prm)
// }

export async function report_excel(URLx, prm) {
    return ajaxPost(URLx, prm)
}