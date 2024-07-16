function one_money(inp, format) {
    return numeral(inp).format(format ? format : '0,000')
}
window.one_money = one_money

function one_mask_money(x) {
    let l = x.toString().length
    let y = []
    for (let i=0; i<l; i++) {
        y.push('#')
        if (i%3==2 && (i+1)<l) y.push(',')
    }

    let z = y.reverse()
    z.push('#')
    return z.join('')
}

function standardizePhoneNumber(phoneNumber) {
    // Remove all non-digit characters
    let cleanedNumber = phoneNumber.replace(/\D/g, '');

    // Check for different possible formats and adjust accordingly
    if (cleanedNumber.startsWith('0')) {
        // Remove leading '0' and prepend '62'
        cleanedNumber = '62' + cleanedNumber.slice(1);
    } else if (cleanedNumber.startsWith('62')) {
        // Already in the correct format
    } else if (cleanedNumber.startsWith('+62')) {
        // Remove the leading '+'
        cleanedNumber = cleanedNumber.slice(1);
    } else {
        // Prepend '62' if no country code
        cleanedNumber = '62' + cleanedNumber;
    }

    return cleanedNumber;
}



window.one_mask_money = one_mask_money
if (typeof moment !== 'undefined') {
    // Use moment here
    window.moment = moment
} else {
    console.error('Moment.js library is not loaded or defined.');
    // Handle the error gracefully, such as notifying the user or using a fallback
}
window.phoneFormat = standardizePhoneNumber