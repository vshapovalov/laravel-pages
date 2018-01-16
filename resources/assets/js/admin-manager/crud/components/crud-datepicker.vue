<template>
    <div ref="pickergroup" :data-field="mode">
        <input  ref="pickerinput" class="input" :type="mode" :value="value">
        <div ref="picker"></div>
    </div>
</template>

<script>

    export default {
        name: 'crud-datepicker',
        model: {
            prop: "value",
            event: "change"
        },
        props: ["value", "field"],
        data: function () {
            return {
                dValue: '',
                mode: 'date'
            }
        },
        computed: {},
        methods: {
            onChangeDate(sValue, dValue, oInputElement)
            {
                if (this.field.readonly)
                {
                    toastr.info("Редактирование запрещено");
                    return;
                }

                this.$emit("change", sValue);
            }
        },
        beforeMount(){
            if (this.field.additional && this.field.additional.mode){
                this.mode = this.field.additional.mode;
            }
        },
        mounted() {

            $(this.$refs.picker).DateTimePicker({
                mode: this.mode,
                dateSeparator: "-",
                dateTimeFormat: "yyyy-MM-dd HH:mm:ss",
                dateFormat: "yyyy-MM-dd",
                timeFormat: "HH:mm",
                shortDayNames: ["Вск", "Пн", "Вт", "Ср", "Чт", "Пт", "Суб"],
                fullDayNames: ["Воскресенье", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота"],
                shortMonthNames:["Янв", "Фев", "Мар", "Апр", "Май", "Юн", "Юл", "Авг", "Сен", "Окт", "Ноя", "Дек"],
                fullMonthNames: ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"],
                titleContentDate: "Дата",
                titleContentTime: "Время",
                titleContentDateTime: "Дата и время",
                buttonsToDisplay: ["HeaderCloseButton", "SetButton", "ClearButton"],
                setButtonContent: "Указать",
                clearButtonContent: "Очистить",
                isPopup: true,
                parentElement: this.$refs.pickergroup,

                isInline: false,
                inputElement: this.$refs.pickerinput,
                label: {
                    "year": "Год",
                    "month": "Месяц",
                    "day": "День",
                    "hour": "Час",
                    "minutes": "Минуты",
                    "seconds": "Секуды",
                    "meridiem": "Меридиан"
                },
                settingValueOfElement: this.onChangeDate
            });
        }
    }
</script>
