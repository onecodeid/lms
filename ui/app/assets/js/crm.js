export const shortcodes = ['customer_name',
    'customer_phone',
    'admin_name',
    'admin_phone',
    'order_number',
    'order_total',
    'order_total_text',
    'order_items',
    'company_name'
]

export function wa_format(shortcodes, data) {
    let rst = data.content
    let x
    for (let sh of shortcodes) {
        x = "[" + sh + "]"
        let replacement = !!data.params[sh] ? data.params[sh] : ''

        while (rst.includes(x)) {
            rst = rst.replace(x, replacement)
        }

    }

    return rst
}