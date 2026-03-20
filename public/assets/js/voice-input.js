(function () {
    "use strict";

    var Recognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (!Recognition) {
        return;
    }

    var ACTIVE_CLASS = "voice-mic-active";
    var INJECTED_CLASS = "voice-mic-injected";
    var STYLE_ID = "voice-input-styles";
    var BUTTON_CLASS = "voice-mic-button";
    var TARGET_SELECTOR = "input, textarea";

    function injectStyles() {
        if (document.getElementById(STYLE_ID)) {
            return;
        }

        var style = document.createElement("style");
        style.id = STYLE_ID;
        style.textContent = [
            ".voice-mic-host{position:relative;display:block;width:100%;}",
            ".voice-mic-host > input," +
                ".voice-mic-host > textarea{padding-right:44px !important;}",
            "." + BUTTON_CLASS + "{" +
                "position:absolute;right:10px;top:50%;transform:translateY(-50%);" +
                "width:28px;height:28px;border:none;border-radius:999px;" +
                "background:#f5f5f5;color:#7f0000;cursor:pointer;z-index:5;" +
                "display:inline-flex;align-items:center;justify-content:center;" +
                "font-size:13px;line-height:1;transition:all .2s ease;}",
            "." + BUTTON_CLASS + ":hover{background:#ffe3e3;}",
            "." + BUTTON_CLASS + "." + ACTIVE_CLASS + "{" +
                "background:#7f0000;color:#fff;box-shadow:0 0 0 3px rgba(127,0,0,.2);}",
            "textarea + ." + BUTTON_CLASS + "{top:10px;transform:none;}"
        ].join("");

        document.head.appendChild(style);
    }

    function isEligibleField(field) {
        if (!field || field.dataset.voiceMicSkip === "true") {
            return false;
        }

        if (field.readOnly || field.disabled) {
            return false;
        }

        var tagName = field.tagName.toLowerCase();
        if (tagName === "textarea") {
            return true;
        }

        if (tagName !== "input") {
            return false;
        }

        var type = (field.getAttribute("type") || "text").toLowerCase();
        var blocked = {
            password: true,
            hidden: true,
            file: true,
            checkbox: true,
            radio: true,
            submit: true,
            button: true,
            reset: true,
            color: true,
            range: true,
            date: true,
            datetime: true,
            "datetime-local": true,
            month: true,
            week: true,
            time: true,
            number: true
        };

        return !blocked[type];
    }

    function dispatchInputEvents(field) {
        field.dispatchEvent(new Event("input", { bubbles: true }));
        field.dispatchEvent(new Event("change", { bubbles: true }));
    }

    function capitalizeWords(text) {
        return text.replace(/\b([a-z])/g, function (_, first) {
            return first.toUpperCase();
        });
    }

    function normalizeSpokenSymbols(text) {
        var normalized = " " + text + " ";

        var replacements = [
            { pattern: /\s+at\s+sign\s+/gi, value: " @ " },
            { pattern: /\s+forward\s+slash\s+/gi, value: " / " },
            { pattern: /\s+slash\s+/gi, value: " / " },
            { pattern: /\s+back\s+slash\s+/gi, value: " \\\\ " },
            { pattern: /\s+underscore\s+/gi, value: " _ " },
            { pattern: /\s+hyphen\s+/gi, value: " - " },
            { pattern: /\s+dash\s+/gi, value: " - " },
            { pattern: /\s+dot\s+/gi, value: " . " },
            { pattern: /\s+period\s+/gi, value: " . " },
            { pattern: /\s+comma\s+/gi, value: " , " },
            { pattern: /\s+colon\s+/gi, value: " : " },
            { pattern: /\s+semicolon\s+/gi, value: " ; " },
            { pattern: /\s+question\s+mark\s+/gi, value: " ? " },
            { pattern: /\s+exclamation\s+point\s+/gi, value: " ! " },
            { pattern: /\s+open\s+parenthesis\s+/gi, value: " ( " },
            { pattern: /\s+close\s+parenthesis\s+/gi, value: " ) " },
            { pattern: /\s+space\s*bar\s+/gi, value: " [[SPACEBAR]] " },
            { pattern: /\s+back\s*space\s+/gi, value: " [[BACKSPACE]] " },
            { pattern: /\s+delete\s+word\s+/gi, value: " [[DELETE_WORD]] " },
            { pattern: /\s+clear\s+field\s+/gi, value: " [[CLEAR_FIELD]] " },
            { pattern: /\s+new\s+line\s+/gi, value: " [[NEWLINE]] " },
            { pattern: /\s+select\s+all\s+/gi, value: " [[SELECT_ALL]] " },
            { pattern: /\s+stop\s+listening\s+/gi, value: " [[STOP_LISTENING]] " }
        ];

        replacements.forEach(function (item) {
            normalized = normalized.replace(item.pattern, item.value);
        });

        return normalized
            .replace(/\s+([@\/\\._\-,:;!?\)\]])/g, "$1")
            .replace(/([\(\[])+\s+/g, "$1")
            .replace(/\s{2,}/g, " ")
            .trim();
    }

    function isEmailLikeField(field) {
        var type = (field.getAttribute("type") || "").toLowerCase();
        if (type === "email") {
            return true;
        }

        var raw = [
            field.name || "",
            field.id || "",
            field.getAttribute("autocomplete") || "",
            field.getAttribute("placeholder") || ""
        ].join(" ").toLowerCase();

        return /(email|e-mail)/.test(raw);
    }

    function isUsernameLikeField(field) {
        var raw = [
            field.name || "",
            field.id || "",
            field.getAttribute("autocomplete") || "",
            field.getAttribute("placeholder") || ""
        ].join(" ").toLowerCase();

        return /(username|user_name|user id|userid|login name|handle)/.test(raw);
    }

    function isCompactField(field) {
        return isEmailLikeField(field) || isUsernameLikeField(field);
    }

    function normalizeCompactFieldSpacing(text) {
        // Keep only explicit spaces spoken as "spacebar".
        return text
            .replace(/\[\[SPACEBAR\]\]/g, "\u0007")
            .replace(/\[\[(?:BACKSPACE|DELETE_WORD|CLEAR_FIELD|NEWLINE|SELECT_ALL|STOP_LISTENING)\]\]/g, "")
            .replace(/\s+/g, "")
            .replace(/\u0007/g, " ");
    }

    function appendTranscriptChunk(currentValue, chunk, joinWithSpace) {
        if (!chunk) {
            return currentValue;
        }

        if (!currentValue) {
            return chunk;
        }

        return joinWithSpace ? currentValue + " " + chunk : currentValue + chunk;
    }

    function applyTranscriptCommands(field, currentValue, transcript, joinWithSpace) {
        var value = currentValue || "";
        var parts = String(transcript || "").split(/(\[\[(?:BACKSPACE|DELETE_WORD|CLEAR_FIELD|NEWLINE|SELECT_ALL|STOP_LISTENING)\]\])/);
        var isTextarea = field.tagName && field.tagName.toLowerCase() === "textarea";

        for (var i = 0; i < parts.length; i += 1) {
            var part = parts[i];

            if (part === "[[BACKSPACE]]") {
                if (value.length > 0) {
                    value = value.slice(0, -1);
                }
                continue;
            }

            if (part === "[[DELETE_WORD]]") {
                value = value.replace(/\s+$/, "").replace(/\S+\s*$/, "");
                continue;
            }

            if (part === "[[CLEAR_FIELD]]") {
                value = "";
                continue;
            }

            if (part === "[[NEWLINE]]") {
                if (isTextarea) {
                    value += "\n";
                } else if (joinWithSpace && value && !/\s$/.test(value)) {
                    value += " ";
                }
                continue;
            }

            if (part === "[[SELECT_ALL]]") {
                field.select();
                continue;
            }

            if (part === "[[STOP_LISTENING]]") {
                return { value: value, stopListen: true };
            }

            var chunk = part;
            if (joinWithSpace) {
                chunk = chunk.trim();
            }

            value = appendTranscriptChunk(value, chunk, joinWithSpace);
        }

        return value;
    }

    function isNameLikeField(field) {
        var raw = [
            field.name || "",
            field.id || "",
            field.getAttribute("autocomplete") || "",
            field.getAttribute("placeholder") || ""
        ].join(" ").toLowerCase();

        if (!raw) {
            return false;
        }

        if (/(email|e-mail|username|user_name|studentnum|student number|contact number|phone|mobile)/.test(raw)) {
            return false;
        }

        return /(name|first_name|middle_name|last_name|surname|given name|fullname|full name|suffix)/.test(raw);
    }

    function formatTranscript(field, transcript) {
        var text = (transcript || "").trim();
        if (!text) {
            return "";
        }

        text = normalizeSpokenSymbols(text);

        if (isCompactField(field)) {
            return normalizeCompactFieldSpacing(text);
        }

        text = text.replace(/\[\[SPACEBAR\]\]/g, " ");

        if (isNameLikeField(field)) {
            return capitalizeWords(text);
        }

        return text;
    }

    function attachMic(field) {
        if (!isEligibleField(field) || field.classList.contains(INJECTED_CLASS)) {
            return;
        }

        if (!field.parentElement) {
            return;
        }

        var host = field.parentElement;
        if (!host.classList.contains("voice-mic-host")) {
            var wrapper = document.createElement("div");
            wrapper.className = "voice-mic-host";
            host.insertBefore(wrapper, field);
            wrapper.appendChild(field);
            host = wrapper;
        }

        var button = document.createElement("button");
        button.type = "button";
        button.className = BUTTON_CLASS;
        button.setAttribute("aria-label", "Start voice input");
        button.setAttribute("title", "Start voice input");
        button.textContent = "\uD83C\uDFA4";

        var recognition = null;
        var listening = false;

        function stopListening() {
            if (recognition && listening) {
                recognition.stop();
            }
        }

        button.addEventListener("click", function () {
            if (listening) {
                stopListening();
                return;
            }

            recognition = new Recognition();
            recognition.lang = document.documentElement.lang || "en-US";
            recognition.continuous = false;
            recognition.interimResults = false;

            recognition.onstart = function () {
                listening = true;
                button.classList.add(ACTIVE_CLASS);
                button.setAttribute("title", "Listening... click to stop");
                button.setAttribute("aria-label", "Listening");
            };

            recognition.onresult = function (event) {
                var transcript = "";
                for (var i = event.resultIndex; i < event.results.length; i += 1) {
                    transcript += event.results[i][0].transcript;
                }

                transcript = formatTranscript(field, transcript);
                if (!transcript) {
                    return;
                }

                var joinWithSpace = !isCompactField(field);
                var currentValue = joinWithSpace ? (field.value || "").trim() : (field.value || "");
                var result = applyTranscriptCommands(field, currentValue, transcript, joinWithSpace);
                var newValue = typeof result === "string" ? result : result.value;
                field.value = newValue;
                dispatchInputEvents(field);

                if (result && result.stopListen) {
                    stopListening();
                }
            };

            recognition.onerror = function () {
                listening = false;
                button.classList.remove(ACTIVE_CLASS);
                button.setAttribute("title", "Start voice input");
                button.setAttribute("aria-label", "Start voice input");
            };

            recognition.onend = function () {
                listening = false;
                button.classList.remove(ACTIVE_CLASS);
                button.setAttribute("title", "Start voice input");
                button.setAttribute("aria-label", "Start voice input");
            };

            recognition.start();
        });

        field.insertAdjacentElement("afterend", button);
        field.classList.add(INJECTED_CLASS);
    }

    function scanAndAttach(root) {
        var scope = root || document;
        var fields = scope.querySelectorAll(TARGET_SELECTOR);
        fields.forEach(attachMic);
    }

    function initObserver() {
        var observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                mutation.addedNodes.forEach(function (node) {
                    if (!(node instanceof Element)) {
                        return;
                    }

                    if (node.matches && node.matches(TARGET_SELECTOR)) {
                        attachMic(node);
                    }

                    if (node.querySelectorAll) {
                        scanAndAttach(node);
                    }
                });
            });
        });

        observer.observe(document.body, { childList: true, subtree: true });
    }

    function init() {
        injectStyles();
        scanAndAttach(document);
        initObserver();
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", init);
    } else {
        init();
    }
})();
