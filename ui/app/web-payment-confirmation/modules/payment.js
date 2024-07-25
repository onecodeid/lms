import { one_token, one_user, URL } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        paymentCode: '',
        paymentDate: '',
        paymentTime: '00:00',
        paymentBankSender: '',
        paymentBankName: '',
        paymentNote: '',
        paymentReceipt: null,
        paymentAmount: 0,

        selectedAccount: null,
        selectedBank: null,

        invoiceId: 0,
        invoiceNumber: '',
        invoiceTotal: 0,

        step: 0
    },
    mutations: {
        set_common (state, v) {
            let name = v[0]
            let val = v[1]
            if (typeof(val) == "string")
                eval(`state.${name} = "${val}"`)
            else
                eval(`state.${name} = ${val}`)
        },

        set_object(state, v) {
            let name = v[0]
            let val = v[1]
            state[name] = val
        }
    },
    actions: {
        async searchInvoice({state, commit, dispatch}) {
            return dispatch("system/postme", {
                url: 'sales/invoice/search',
                prm: {
                    search: state.invoiceNumber,
                    sdate: '2024-01-01', edate: '2055-01-01', page: 1, token: one_token()
                },

                callback: function(d) {
                    console.log(d)
                    return d
                },

                failback: function(e) {
                    // context.commit('set_common', ['loading_city', false])
                }
            }, { root: true })
        },
        
        async confirm({state, commit, dispatch, rootState}) {

            return dispatch("system/postme", {
                url: 'finance/payment/save',
                prm: {
                    token: one_token(),
                    transfer_date: state.paymentDate,
                    transfer_time: state.paymentTime,
                    transfer_name: state.paymentBankSender,
                    transfer_note: state.paymentNote,
                    account_id: state.selectedAccount.account_id,
                    bank_id: state.selectedBank.M_BankID,
                    order_id: state.invoiceId,
                    payment_type_id: 2,
                    amount: state.paymentAmount,
                    receipt_img: state.paymentReceipt
                },

                callback: function(d) {
                    console.log(d)
                    return d
                },

                failback: function(e) {
                    // context.commit('set_common', ['loading_city', false])
                }
            }, { root: true })
        },

        
    }
}