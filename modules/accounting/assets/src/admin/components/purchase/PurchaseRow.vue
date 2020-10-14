<template>
    <tr>
        <th scope="row" class="col--check with-multiselect column-primary product-select">
            <multi-select v-model="line.selectedProduct" :options="products" @input="setProductInfo" />
        </th>
        <td class="col--qty">
            <input min="0" type="number"
                   v-model="line.qty"
                   @keyup="calculateAmount"
                   name="qty"
                   class="wperp-form-field" :required="line.selectedProduct ? true : false">
        </td>
        <td class="col--uni_price" data-colname="Unit Price">
            <input min="0" type="number" v-model="line.unitPrice"
                @keyup="calculateAmount"
                step="0.01"
                class="wperp-form-field text-right" :required="line.selectedProduct ? true : false">
        </td>
        <td class="col--amount" data-colname="Amount">
            <input type="number" min="0" step="0.01" v-model="line.amount" class="wperp-form-field text-right" readonly>
        </td>
        <td class="col--tax" data-colname="Tax">
            <input type="checkbox" v-model="line.applyTax"  class="wperp-form-field">
        </td>
        <td class="col--actions delete-row" data-colname="Action">
            <span class="wperp-btn" @click="removeRow"><i class="flaticon-trash"></i></span>
        </td>
    </tr>
</template>

<script>
import MultiSelect from 'admin/components/select/MultiSelect.vue';
import {mapState} from "vuex";

export default {
    name: 'PurchaseRow',

    props: {
        products: {
            type: Array,
            default: () => []
        },

        line: {
            type: Object,
            default: () => {}
        },

        taxSummary: {
            type: Array,
            default: () => []
        }
    },

    components: {
        MultiSelect
    },


    computed: mapState({
        taxRateID         : state => state.sales.taxRateID,
        invoiceTotalAmount: state => state.sales.invoiceTotalAmount
    }),

    created() {
        // check if editing
        if (this.$route.params.id) {
            this.prepareRowEdit(this.line);
        }
    },

    watch: {
        taxRateID(newData) {
            this.calculateAmount();
            console.log(newData)
        },

        invoiceTotalAmount() {
            this.calculateAmount();
        }
    },

    methods: {
        prepareRowEdit(row) {
            row.unitPrice = row.price;
            row.selectedProduct = { id: parseInt(row.product_id), name: row.name };

            this.calculateAmount();
        },

        setProductInfo() {
            this.line.qty = 1;

            if (this.$route.params.id) {
                this.line.unitPrice = parseFloat(this.line.selectedProduct.cost_price);
            } else {
                this.line.unitPrice = parseFloat(this.line.selectedProduct.unitPrice);
                this.line.applyTax = true;
            }

            this.line.qty               = 1;
            this.line.taxCatID          = this.line.selectedProduct.tax_cat_id;
            this.line.product_type_name = this.line.selectedProduct.product_type_name;

            if (this.line.taxCatID) {
                this.line.applyTax = true;
            }

            this.calculateAmount();
        },

        getAmount() {
            if (!this.line.qty) {
                this.line.qty = 0;
            }

            if (!this.line.qty || !this.line.unitPrice) {
                this.line.amount = 0;

                return false;
            }

            // Set Amount
            return parseInt(this.line.qty) * parseFloat(this.line.unitPrice);
        },
        getTaxRate() {
            /**
             * |-------------------------------------------------------------------------
             * * taxSummary: ( props ) The tax summary result from database
             * * tax: Every item in taxSummary ( loop )
             * * this.line: Think it is as `product` in every row
             * * taxRateID: Selected value from `Tax Rate Dropdown` dropdown
             * |-------------------------------------------------------------------------
             */
            const taxInfo = this.taxSummary.find(tax => {
                if (tax.sales_tax_category_id === this.line.taxCatID && tax.tax_rate_id === this.taxRateID) {
                    return tax;
                }
            });

            this.taxRate = 0;

            if (taxInfo) {
                this.taxRate = parseFloat(taxInfo.tax_rate);
            }

            this.line.taxRate = this.taxRate;
        },
        calculateTax() {
            const amount = this.getAmount();
            if (!amount) return;

            const taxAmount = ( amount   *  this.taxRate) / 100;

            this.line.taxAmount = 0;

            // If tax checkbox is checked
            if (this.line.applyTax) {
                this.line.taxAmount = taxAmount.toFixed(2);
            }
        },
        calculateAmount() {


            const amount = this.getAmount();
            if (!amount) return;

            this.line.amount = amount.toFixed(2);

            // Send amount to parent for total calculation
            this.$root.$emit('total-updated', this.line.amount);

            this. getTaxRate() ;

            this. calculateTax() ;

            this.$forceUpdate(); // why? should use computed? or vue.set()?
        },

        removeRow() {
            this.$root.$emit('remove-row', this.$vnode.key);
        }
    }
};
</script>

<style lang="less" scoped>
.wperp-form-table {
    .col--tax {
        input {
            width: initial;
            padding: 0 !important;
            border-color: rgba(26, 158, 212, 0.45);
        }
    }
}

    .product-select {
        font-weight: normal !important;
    }
</style>
