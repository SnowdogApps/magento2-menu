(function() {
    /* jshint ignore:start */

    /* jshint ignore:end */

    define("require_vuejs", function(){
        return plugin;
    });
    /*vim: set ts=4 ex=4 tabshift=4 expandtab :*/

    /*globals: define, require */
    /*
     * css-parser.js
     *
     * Distributed under terms of the MIT license.
     */
    /* jshint ignore:start */

    /* jshint ignore:end */

    var css_parser = (function(){
        "use strict";
        var extractCss = function(text) {
            var start = text.indexOf("<style>");
            var end = text.indexOf("</style>");

            if( start === -1 ) {
                return false;
            } else {
                return text.substring(start + 7, end);
            }
        };

        var appendCSSStyle = function(css) {
            if(css && typeof document !== "undefined") {
                var style = document.createElement("style");
                var head = document.head || document.getElementsByTagName("head")[0];

                style.type = "text/css";
                if (style.styleSheet){
                    style.styleSheet.cssText = css;
                } else {
                    style.appendChild(document.createTextNode(css));
                }

                head.appendChild(style);
            }
        };

        var createDocumentMock = function() {
            return {
                createElement: function() {},
                head: {},
                getElementsByTagName: function() {},
                createTextNode: function() {}
            };
        };

        return {
            extractCss: extractCss,
            appendCSSStyle: appendCSSStyle,
            functionString: function(text) {
                if (typeof document === "undefined")  // you are running optimization ( r.js )
                    var document = createDocumentMock(); // var put it on start of scope

                var css = extractCss(text);
                if ( css === false ) {
                    return "";
                } else {
                    css = css
                        .replace(/([^\\])'/g, "$1\\'")
                        .replace(/[\n\r]+/g, "")
                        .replace(/ {2,20}/g, " ");
                }

                var result = "(" + appendCSSStyle.toString() + ")('" + css + "');";
                return result;
            },
            parse: function(text) {
                var css = extractCss(text);
                appendCSSStyle(css);
            }
        };
    })();

    /*
     * template-parser.js
     *
     * Distributed under terms of the MIT license.
     */
    /* jshint ignore:start */

    /* jshint ignore:end */

    var template_parser = (function(){

        var extractTemplate = function(text) {
            var start = text.indexOf("<template>");
            var end   = text.lastIndexOf("</template>");
            return text.substring(start + 10, end)
                .replace(/([^\\])'/g, "$1\\'")
                .replace(/^(.*)$/mg, "'$1' + ") // encapsulate template code between ' and put a +
                .replace(/ {2,20}/g, " ") + "''";
        };


        return {
            extractTemplate: extractTemplate
        };

    })();

    /*
     * script-parser.js
     * Copyright (C) 2017 Edgard Leal
     *
     * Distributed under terms of the MIT license.
     */

    /* jshint ignore:start */

    /* jshint ignore:end */

    var script_parser = (function(){
        return {
            findCloseTag: function(text, start) {
                var i = start;
                while(i < text.length && text[i++] !== ">");
                return i;
            },
            extractScript: function(text) {
                var start = text.indexOf("<script"); // I don't know why, but someone could use attributes on script tag
                var sizeOfStartTag = this.findCloseTag(text, start);
                var end = text.indexOf("</script>");
                return text.substring(sizeOfStartTag, end);
            }
        };
    })();

    /*
     * vue.js
     *
     * Distributed under terms of the MIT license.
     */
    /* global Promise */
    /* jshint ignore:start */

    /* jshint ignore:end */

    var plugin = (function(){
        "use strict";

        var modulesLoaded = {};

        var functionTemplate = ["(function(template){", "})("];

        var parse = function(text) {
            var template = template_parser.extractTemplate(text);
            var source = script_parser.extractScript(text);
            var functionString = css_parser.functionString(text);

            return functionTemplate[0] +
                source +
                functionString +
                functionTemplate[1] +
                template + ");";
        };

        var loadLocal = function(url, name) {
            var fs = require.nodeRequire("fs");
            var text = fs.readFileSync(url, "utf-8");
            if(text[0] === "\uFEFF") { // remove BOM ( Byte Mark Order ) from utf8 files
                text = text.substring(1);
            }
            var parsed = parse(text).replace(/(define\()\s*(\[.*)/, "$1\"vue!" + name + "\", $2");
            return parsed;
        };

        return {
            normalize: function(name, normalize) {
                return normalize(name);
            },
            write: function(pluginName, moduleName, write) {
                write.asModule(pluginName + "!" + moduleName, modulesLoaded[moduleName]);
            },
            load: function (name, req, onload, config) {
                var url, extension;

                if (config.paths && config.paths[name]) {
                    name = config.paths[name];
                }

                // if file name has an extension, don't add .vue
                if(/.*(\.vue)|(\.html?)/.test(name)) {
                    extension = "";
                } else {
                    extension = ".vue";
                }

                url = req.toUrl(name + extension);

                // this is used to browser to create a way to debug the file
                var sourceHeader = config.isBuild?"" : "//# sourceURL=" + location.origin + url + "\n";
                var loadRemote;

                if(config.isBuild) {
                    loadRemote = function(url, callback) {
                        return new Promise(function(resolve, reject) {
                            try {
                                var fs = require.nodeRequire("fs");
                                var text = fs.readFileSync(url, "utf-8").toString();
                                if(text[0] === "\uFEFF") { // remove BOM ( Byte Mark Order ) from utf8 files
                                    text = text.substring(1);
                                }
                                var parsed = parse(text).replace(/(define\()\s*(\[.*)/, "$1\"" + name + "\", $2");
                                callback(parsed);
                                resolve(parsed);
                            } catch(error) {
                                reject(error);
                            }
                        });
                    };
                } else {
                    loadRemote = function(path, callback) {
                        var xhttp = new XMLHttpRequest();
                        xhttp.onreadystatechange = function() {
                            if (this.readyState === 4 && (this.status === 200 || this.status === 304)) {
                                callback(parse(xhttp.responseText));
                            }
                        };
                        xhttp.open("GET", path, true);
                        xhttp.send();
                    };
                }

                req([], function() {
                    if(config.isBuild) {
                        var data = loadLocal(url, name);
                        modulesLoaded[name] = data;
                        onload.fromText(data);
                    } else {
                        loadRemote(url, function(text){
                            modulesLoaded[name] = sourceHeader + text;
                            onload.fromText(modulesLoaded[name]);
                        });
                    }
                });
            }
        };
    })();

    /**
     * vue.js
     * Copyright (C) 2017
     *
     * Distributed under terms of the MIT license.
     */
    /* jshint ignore:start */

    /* jshint ignore:end */

    define("vue", function(){
        return plugin;
    });
    /* vim: set tabstop=4 softtabstop=4 shiftwidth=4 expandtab : */

})();
