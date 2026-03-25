(function injectVoiceInputGuide() {
        if (document.getElementById('voiceInputGuideModal')) return;
        var guideHtml = `
<!-- Voice Input Guide Modal -->
<div id="voiceInputGuideModal" class="voice-guide-modal" tabindex="-1" role="dialog" aria-labelledby="voiceInputGuideLabel" aria-hidden="true" style="display:none;">
    <div class="voice-guide-modal-dialog">
        <div class="voice-guide-modal-content">
            <div class="voice-guide-modal-header">
                <div class="voice-guide-modal-title">
                    <i class="fa fa-microphone-alt"></i> Voice Input Guide
                </div>
                <button type="button" class="voice-guide-close" aria-label="Close" onclick="document.getElementById('voiceInputGuideModal').style.display='none'">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="voice-guide-modal-body">
                <ul>
                    <li><b>Say field content directly</b> <span class="text-muted">(e.g., Juan Dela Cruz)</span></li>
                    <li><b>Symbols:</b> <span class="text-muted">"at sign" → @, "slash" → /, "underscore" → _</span></li>
                    <li><b>Spacing (email/username):</b> <span class="text-muted">Say "spacebar" to insert a space</span></li>
                    <li><b>Commands:</b>
                        <ul class="voice-guide-commands">
                            <li><span class="cmd">backspace</span> – delete last character</li>
                            <li><span class="cmd">delete word</span> – delete last word</li>
                            <li><span class="cmd">clear field</span> – clear all text</li>
                            <li><span class="cmd">new line</span> – new line (textarea only)</li>
                            <li><span class="cmd">select all</span> – select all text</li>
                            <li><span class="cmd">stop listening</span> – stop voice input</li>
                        </ul>
                    </li>
                    <li><b>Suffixes:</b> <span class="text-muted">"junior" → Jr, "the third" → III, etc.</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<style>
.voice-guide-btn {
    position: fixed;
    bottom: 110px;
    right: 32px;
    z-index: 1200;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #fff;
    color: #7f0000;
    box-shadow: 0 2px 8px rgba(127,0,0,0.13);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    cursor: pointer;
    border: 2px solid #7f0000;
    transition: background 0.2s, color 0.2s;
}
.voice-guide-btn:hover {
    background: #7f0000;
    color: #fff;
}
@media (max-width: 600px) {
    .voice-guide-btn { right: 12px; bottom: 90px; }
}
.voice-guide-modal {
    position: fixed;
    z-index: 1300;
    left: 0; top: 0; width: 100vw; height: 100vh;
    background: rgba(30,0,0,0.18);
    display: flex; align-items: center; justify-content: center;
}
.voice-guide-modal-dialog {
    max-width: 410px; width: 96vw;
    margin: 0 auto;
}
.voice-guide-modal-content {
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 8px 32px rgba(127,0,0,0.13);
    overflow: hidden;
    border: 1.5px solid #7f0000;
    animation: fadeInScale 0.25s cubic-bezier(0.4,0,0.2,1);
}
.voice-guide-modal-header {
    background: linear-gradient(90deg, #7f0000 0%, #dc2626 100%);
    color: #fff;
    padding: 18px 24px 12px 24px;
    display: flex; align-items: center; justify-content: space-between;
}
.voice-guide-modal-title {
    font-size: 1.18rem; font-weight: 700; display: flex; align-items: center; gap: 10px;
    color: #fff !important;
}
.voice-guide-modal-title .fa-microphone-alt {
    color: #fff !important;
}
.voice-guide-close {
    background: none; border: none; color: #fff; font-size: 1.5rem; cursor: pointer; line-height: 1; opacity: 0.85;
    transition: opacity 0.2s;
}
.voice-guide-close:hover { opacity: 1; }
.voice-guide-modal-body {
    padding: 22px 24px 24px 24px;
    color: #333;
    background: #fafafa;
    font-size: 1rem;
}
.voice-guide-modal-body ul { padding-left: 18px; margin-bottom: 0; }
.voice-guide-modal-body li { margin-bottom: 10px; }
.voice-guide-commands { margin-top: 6px; margin-bottom: 0; padding-left: 18px; }
.voice-guide-commands .cmd {
    background: #f5e6e6;
    color: #7f0000;
    border-radius: 6px;
    padding: 2px 8px;
    font-family: 'Poppins', monospace;
    font-size: 0.98em;
    margin-right: 6px;
}
.text-muted { color: #888; font-weight: 400; font-size: 0.97em; }
@keyframes fadeInScale {
    0% { opacity: 0; transform: scale(0.95); }
    100% { opacity: 1; transform: scale(1); }
}
</style>
<div class="voice-guide-btn" title="Voice Input Guide" onclick="document.getElementById('voiceInputGuideModal').style.display='block'">
    <i class="fa fa-microphone-alt"></i>
</div>
<script>
window.addEventListener('click', function(e) {
    var modal = document.getElementById('voiceInputGuideModal');
    if (modal && e.target === modal) {
        modal.style.display = 'none';
    }
});
</script>`;
        var temp = document.createElement('div');
        temp.innerHTML = guideHtml;
        while (temp.firstChild) {
                document.body.appendChild(temp.firstChild);
        }
})();
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

        // Suffix normalization (spoken to written)
        var suffixReplacements = [
            { pattern: /\s+junior\s+/gi, value: " Jr " },
            { pattern: /\s+senior\s+/gi, value: " Sr " },
            { pattern: /\s+the\s+second\s+|\s+second\s+/gi, value: " II " },
            { pattern: /\s+the\s+third\s+|\s+third\s+/gi, value: " III " },
            { pattern: /\s+the\s+fourth\s+|\s+fourth\s+/gi, value: " IV " },
            { pattern: /\s+the\s+fifth\s+|\s+fifth\s+/gi, value: " V " }
        ];
        suffixReplacements.forEach(function (item) {
            normalized = normalized.replace(item.pattern, item.value);
        });

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
