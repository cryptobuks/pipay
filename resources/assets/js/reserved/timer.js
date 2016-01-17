Architekt.module.reserv('Timer', function () {
    //Procedure : Executable time binding procedure object
    Procedure = function (option) {
        if (typeof (option) === "undefined") option = {};
        this._procedure = typeof (option.procedure) !== "undefined" ? option.procedure : function () { };
        this._duration = typeof (option.duration) !== "undefined" ? parseInt(option.duration) : 0;
        this._loop = typeof (option.loop) !== "undefined" ? !!option.loop : false;
        this._handler = false;
    };
    Procedure.prototype.bind = function (func) {
        this._procedure = func;
        return this;
    };
    Procedure.prototype.unbind = function () {
        this._procedure = function () { };
        return this;
    };
    Procedure.prototype.getDuration = function () {
        return this._duration;
    };
    Procedure.prototype.getLoop = function () {
        return this._loop;
    };
    Procedure.prototype.setDuration = function (newDuration) {
        this._duration = parseInt(newDuration);
        return this;
    };
    Procedure.prototype.setLoop = function (newLoop) {
        this._loop = !!newLoop;
        return this;
    };
    //Procedure.execute({ duration: as int, arguments: as object }) : Execute a procedure
    Procedure.prototype.execute = function (option) {
        if (typeof (option) === "undefined") option = {};
        var duration = typeof option.duration !== "undefined" ? parseInt(option.duration) : this._duration;
        var args = typeof option.arguments !== "undefined" ? option.arguments : {};
        var self = this;

        var ProcedureTimerFunction = this._loop ? setInterval : setTimeout;

        this._handler = ProcedureTimerFunction(function () {
            self._procedure.call(this, args);
        }, this._duration);

        return this;
    };
    Procedure.prototype.stop = function () {
        if (this._handler) clearInterval(this._handler) || clearTimeout(this._handler);
        return this;
    };

    return {
        Procedure: Procedure,
    };
});