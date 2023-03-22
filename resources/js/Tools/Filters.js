import {Mask} from "maska";

export default class Filters {
    static maskPhone(phone) {
        const masked = String(new Mask({mask: '(##) # ####-####'}).masked(phone)).substring(0, 16);

        return masked;
    }
}
