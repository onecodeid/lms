function one_money(inp, format) {
    return numeral(inp).format(format ? format : '0,000')
}
window.one_money = one_money