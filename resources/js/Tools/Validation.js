export default class Validation {
    fieldName
    rules = []

    constructor(fieldName) {
        this.fieldName = fieldName;
        this.rules = [];
    }

    required() {
        this.rules.push(value => {
            let condition = Array.isArray(value) ? value.length > 0 : value;
            return !!condition || `O campo ${this.fieldName} é obrigatório`
        });
        return this;
    }

    get() {
        return this.rules;
    }
}
