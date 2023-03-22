import moment from "moment";

export default class Validation {
    fieldName;
    rules;
    _dateFormat;
    _isMoney;
    _isNullable;

    constructor(fieldName) {
        this.fieldName = fieldName;
        this.rules = [];
        this._isNullable = true;
    }

    required() {
        this._isNullable = false
        this.rules.push(value => {
            let condition = Array.isArray(value) ? value.length > 0 : value;
            return !!condition || `O campo ${this.fieldName} é obrigatório`
        });
        return this;
    }

    date(format) {
        this._dateFormat = format
        this.rules.push(value => {
            const stringSplit = value.split('/');
            const day = parseInt(stringSplit[0]);
            const month = parseInt(stringSplit[1]) - 1;
            const year = parseInt(stringSplit[2]);
            const date = moment(value, this._dateFormat).toDate();

            return (/\d{2}\/\d{2}\/\d{4}/.test(value)
                && date.getFullYear() === year
                && date.getMonth() === month
                && date.getDate() === day
            ) || `O campo ${this.fieldName} deve ser uma data válida`
        });
        return this;
    }

    greatestOrEqualThan(value) {
        this.rules.push(v => {
            if (this._dateFormat) {
                let date = moment(v, this._dateFormat).toDate()

                if (date instanceof Date) {
                    v = date
                }
            } else if (this._isMoney) {
                debugger
                v = parseFloat(v.replace(',', '.'))
            }

            return v >= value || `O campo ${this.fieldName} deve ser maior que ${value}`
        });
        return this;
    }

    greatestThan(value) {
        this.rules.push(v => {
            if (this._dateFormat) {
                let date = moment(v, this._dateFormat).toDate()

                if (date instanceof Date) {
                    v = date
                }
            } else if (this._isMoney) {
                debugger
                v = parseFloat(v.replace(',', '.'))
            }

            return v > value || `O campo ${this.fieldName} deve ser maior que ${value}`
        });
        return this;
    }

    time(format) {
        this._dateFormat = format
        this.rules.push(value => {
            let date = moment(value, this._dateFormat).toDate()
            let time = date.toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'});

            return (/\d{2}:\d{2}/.test(value) && time === value) || `O campo ${this.fieldName} deve ser uma hora válida`
        });
        return this;
    }

    money() {
        this._isMoney = true

        this.rules.push(value => {

            return (/,\d{2}/.test(value)) || `O campo ${this.fieldName} deve ser um valor monetário válido`
        });
        return this;
    }

    if(condition, callback) {
        if (condition) {
            callback(this)
        }
        return this;
    }

    email() {
        this.rules.push(value => {
            return (/\S+@\S+\.\S+/.test(value)) || `O campo ${this.fieldName} deve ser um e-mail válido`
        });
        return this;
    }

    get() {
        return this.rules.map(rule => {
            return (value) => {
                if (this._isNullable && !value) {
                    return true
                }

                return rule(value)
            }
        });
    }
}
