var URLx
if (window.location.hostname == "localhost")
    URLx = "http://localhost/lms/api/"
else
    URLx = "https://" + window.location.hostname + "/api/"
export const URL = URLx
export const UPL_URL = "http://lpkglobal.onecode.id/uploads/"

export function one_token() {
    let x = ''
    try {
        x = localStorage.getItem('token')
    } catch (error) {
        x = ''
    }

    return x
}

export async function ajaxPost(u, p, h) {
    try {
        if (h)
            var r = await axios.post(u, p, h)
        else
            var r = await axios.post(u, p)
        if (r.status != 200) {
            return {
                status: "ERR",
                message: r.statusText
            };
        }
        let d = r.data;
        return d;
    } catch (e) {
        return {
            status: "ERR",
            message: e.message
        };
    }
}

export function one_money(inp, format) {
    return numeral(inp).format(format ? format : '0,000')
}

export async function menu_group(prm) {
    return ajaxPost(URL + 'systm/menu/search_group', prm)
}

export function one_user() {
    return JSON.parse(localStorage.getItem('user'))
}

export function current_date() {
    return moment().format('YYYY-MM-DD')
}

export function one_years_month() {
    let months = []
    for (let i = 1; i <= 12; i++) {
        months[i] = moment().subtract(i, 'months').format('MMM YY')
    }
    return months
}

export function insertAtCursor(myField, myValue) {
    //IE support
    if (document.selection) {
        myField.focus();
        sel = document.selection.createRange();
        sel.text = myValue;
    }
    //MOZILLA and others
    else if (myField.selectionStart || myField.selectionStart == '0') {
        var startPos = myField.selectionStart;
        var endPos = myField.selectionEnd;
        myField.value = myField.value.substring(0, startPos) +
            myValue +
            myField.value.substring(endPos, myField.value.length);
        myField.selectionStart = startPos + myValue.length;
        myField.selectionEnd = startPos + myValue.length;
    } else {
        myField.value += myValue;
    }
}