class Candidate {

    toObject() {
        return {
            id: this._id,
            principal_name: this._principal_name,
            principal_faculty: this._principal_faculty,
            principal_program: this._principal_program,
            substitute_name: this._substitute_name,
            substitute_faculty: this._substitute_faculty,
            substitute_program: this._substitute_program,
            voting_option_id: this._voting_option_id,
        }
    }

    get principal_name() {
        return this._principal_name;
    }

    set principal_name(value) {
        this._principal_name = value;
    }

    get principal_faculty() {
        return this._principal_faculty;
    }

    set principal_faculty(value) {
        this._principal_faculty = value;
    }

    get principal_program() {
        return this._principal_program;
    }

    set principal_program(value) {
        this._principal_program = value;
    }

    get substitute_name() {
        return this._substitute_name;
    }

    set substitute_name(value) {
        this._substitute_name = value;
    }

    get substitute_faculty() {
        return this._substitute_faculty;
    }

    set substitute_faculty(value) {
        this._substitute_faculty = value;
    }

    get substitute_program() {
        return this._substitute_program;
    }

    set substitute_program(value) {
        this._substitute_program = value;
    }

    get voting_option_id() {
        return this._voting_option_id;
    }

    set voting_option_id(value) {
        this._voting_option_id = value;
    }

    constructor(id, principal_name, principal_faculty, principal_program, substitute_name, substitute_faculty, substitute_program, voting_option_id) {
        this._id = id ?? null;
        this._principal_name = principal_name;
        this._principal_faculty = principal_faculty;
        this._principal_program = principal_program;
        this._substitute_name = substitute_name;
        this._substitute_faculty = substitute_faculty;
        this._substitute_program = substitute_program;
        this._voting_option_id = voting_option_id;
    }
}

module.exports = Candidate;
