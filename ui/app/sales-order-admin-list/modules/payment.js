// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "../../finance-payment-admin-list/modules/api_payment.js"
import { URL, UPL_URL, one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        URL: URL,
        UPL_URL: UPL_URL,
        edit: false,
        search_status: 0,
        search_error_message: '',
        search: '',
        query: '',
        btn_payment_enable: true
    },
    mutations: {
        set_common(state, v) {
            let name = v[0]
            let val = v[1]
            if (typeof(val) == "string")
                eval(`state.${name} = "${val}"`)
            else
                eval(`state.${name} = ${val}`)
        },

        update_search_error_message(state, msg) {
            state.search_error_message = msg
        },

        update_search(state, search) {
            state.search = search
        },

        update_search_status(state, val) {
            state.search_status = val
        }
    },
    actions: {
        async confirm(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = {}
                prm.token = one_token()
                prm.transfer_date = context.rootState.payment.transfer_date
                prm.transfer_time = context.rootState.payment.transfer_time
                prm.transfer_name = context.rootState.payment.transfer_name
                prm.transfer_note = context.rootState.payment.transfer_note
                prm.account_id = context.rootState.payment.selected_account.account_id
                prm.bank_id = context.rootState.payment.selected_bank.M_BankID
                prm.order_id = context.rootState.salesorder.selected_order.L_InvoiceID
                prm.payment_type_id = context.rootState.salesorder.selected_order.payment_id
                prm.amount = context.rootState.payment.transfer_amount
                prm.receipt_img = context.rootState.payment.receipt_img

                let resp = await api.save_payment(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")
                        // context.commit('set_common', ['snackbar', true])
                        // context.commit('set_selected_order', null)
                        context.commit('payment/set_selected_account', null, {root:true})
                        context.commit('payment/set_selected_bank', null, {root:true})
                        context.commit('payment/set_common', ['transfer_name', ''], {root:true})
                        context.commit('payment/set_common', ['transfer_date', new Date().toISOString().substr(0, 10)], {root:true})
                        context.commit('payment/set_common', ['transfer_time', new Date().toISOString().substr(11, 5)], {root:true})
                        context.commit('payment/set_common', ['transfer_amount', 0, {root:true}])
                        context.commit('salesorder/set_common', ['dialog_payment', false], {root:true})
                        context.commit('salesorder/set_common', ['dialog_item', false], {root:true})
                        context.dispatch('salesorder/search', null, {root:true})

                        // snackbar
                        context.commit('set_snackbar', true, {root:true})
                        context.commit('set_snackbar_text', 'Pembayaran berhasil disimpan', {root:true})
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async confirm_cod(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = {}
                prm.token = one_token()
                prm.transfer_date = context.rootState.payment.transfer_date
                prm.transfer_time = context.rootState.payment.transfer_time
                prm.transfer_name = 'COD.FREE'
                prm.transfer_note = 'COD.FREE'
                prm.account_id = 0
                prm.bank_id = 0
                prm.order_id = context.rootState.salesorder.selected_order.L_InvoiceID
                prm.payment_type_id = context.rootState.salesorder.selected_order.payment_id
                prm.amount = context.rootState.payment.transfer_amount
                prm.receipt_img = ''

                let resp = await api.save_payment(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")
                        context.commit('payment/set_selected_account', null, {root:true})
                        context.commit('payment/set_selected_bank', null, {root:true})
                        context.commit('payment/set_common', ['transfer_name', ''], {root:true})
                        context.commit('payment/set_common', ['transfer_date', new Date().toISOString().substr(0, 10)], {root:true})
                        context.commit('payment/set_common', ['transfer_time', new Date().toISOString().substr(11, 5)], {root:true})
                        context.commit('payment/set_common', ['transfer_amount', 0, {root:true}])
                        context.commit('salesorder/set_common', ['dialog_payment', false], {root:true})
                        context.dispatch('salesorder/search', null, {root:true})

                        // snackbar
                        context.commit('set_snackbar', true, {root:true})
                        context.commit('set_snackbar_text', 'Pembayaran berhasil disimpan', {root:true})
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        }
    }
}