function one_money(e,n){return numeral(e).format(n||"0,000")}function one_mask_money(e){let n=e.toString().length,o=[];for(let e=0;e<n;e++)o.push("#"),e%3==2&&e+1<n&&o.push(",");let t=o.reverse();return t.push("#"),t.join("")}function standardizePhoneNumber(e){let n=e.replace(/\D/g,"");return n.startsWith("0")?n="62"+n.slice(1):n.startsWith("62")||(n=n.startsWith("+62")?n.slice(1):"62"+n),n}window.one_money=one_money,window.one_mask_money=one_mask_money,"undefined"!=typeof moment&&(window.moment=moment),window.phoneFormat=standardizePhoneNumber;