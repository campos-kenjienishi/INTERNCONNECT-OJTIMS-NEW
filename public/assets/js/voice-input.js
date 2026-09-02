(function injectVoiceInputGuide() {
    function createSpeedDialWidget() {
        if (document.getElementById("icFloatingSpeedDial")) {
            return;
        }

        var wrapper = document.createElement("div");
        wrapper.className = "ic-speed-dial";
        wrapper.id = "icFloatingSpeedDial";
        wrapper.innerHTML = [
            '<div class="ic-speed-dial-menu" id="icSpeedDialMenu">',
            '    <button type="button" class="ic-speed-dial-item" id="icBtnAccessibility" title="Toggle Accessibility Tools">',
            '        <span class="ic-item-emblem"><i class="fas fa-universal-access"></i></span>',
            '        <span class="ic-item-label">Accessibility</span>',
            '    </button>',
            '    <button type="button" class="ic-speed-dial-item" id="icBtnVoiceGuide" title="Voice Input & Commands Guide">',
            '        <span class="ic-item-emblem"><i class="fas fa-microphone-alt"></i></span>',
            '        <span class="ic-item-label">Voice Guide</span>',
            '    </button>',
            '</div>',
            '<button type="button" class="ic-speed-dial-trigger" id="icSpeedDialTrigger" aria-label="Quick Tools Menu" title="Quick Tools">',
            '    <span class="ic-trigger-icon"><i class="fas fa-plus"></i></span>',
            '</button>'
        ].join("");

        document.body.appendChild(wrapper);

        // Inject Voice Guide Modal
        if (!document.getElementById("voiceInputGuideModal")) {
            var modalEl = document.createElement("div");
            modalEl.className = "voice-guide-modal";
            modalEl.id = "voiceInputGuideModal";
            modalEl.style.display = "none";
            modalEl.innerHTML = [
                '<div class="voice-guide-backdrop" id="voiceGuideBackdrop"></div>',
                '<div class="voice-guide-modal-dialog">',
                '    <div class="voice-guide-modal-content">',
                '        <div class="voice-guide-modal-header">',
                '            <div class="voice-guide-header-left">',
                '                <div class="voice-guide-header-icon">',
                '                    <i class="fas fa-microphone-alt"></i>',
                '                </div>',
                '                <div>',
                '                    <h4 class="voice-guide-modal-title">Voice Input & Command Guide</h4>',
                '                    <p class="voice-guide-modal-sub">Speak naturally to dictate text and control input fields</p>',
                '                </div>',
                '            </div>',
                '            <button type="button" class="voice-guide-close" id="btnCloseVoiceGuideModal" aria-label="Close">',
                '                <i class="fas fa-times"></i>',
                '            </button>',
                '        </div>',
                '        <div class="voice-guide-modal-body">',
                '            <div class="voice-guide-section">',
                '                <div class="voice-guide-section-title"><i class="fas fa-play-circle text-danger me-1"></i> How To Use</div>',
                '                <div class="voice-steps-grid">',
                '                    <div class="voice-step-card">',
                '                        <div class="voice-step-num">1</div>',
                '                        <div class="voice-step-text">Click the red microphone icon <i class="fa fa-microphone text-danger"></i> on any text input or textarea.</div>',
                '                    </div>',
                '                    <div class="voice-step-card">',
                '                        <div class="voice-step-num">2</div>',
                '                        <div class="voice-step-text">Speak clearly into your microphone. Spoken symbols & punctuations are auto-formatted.</div>',
                '                    </div>',
                '                    <div class="voice-step-card">',
                '                        <div class="voice-step-num">3</div>',
                '                        <div class="voice-step-text">Click the microphone icon again or say <em>"stop listening"</em> to finish.</div>',
                '                    </div>',
                '                </div>',
                '            </div>',
                '            <div class="voice-guide-section">',
                '                <div class="voice-guide-section-title"><i class="fas fa-keyboard text-warning me-1"></i> Spoken Punctuations & Symbols</div>',
                '                <div class="voice-cmd-grid">',
                '                    <div class="voice-cmd-tile"><span class="cmd-speech">"at sign"</span> <i class="fas fa-arrow-right cmd-arrow"></i> <span class="cmd-res">@</span></div>',
                '                    <div class="voice-cmd-tile"><span class="cmd-speech">"dot" / "period"</span> <i class="fas fa-arrow-right cmd-arrow"></i> <span class="cmd-res">.</span></div>',
                '                    <div class="voice-cmd-tile"><span class="cmd-speech">"comma"</span> <i class="fas fa-arrow-right cmd-arrow"></i> <span class="cmd-res">,</span></div>',
                '                    <div class="voice-cmd-tile"><span class="cmd-speech">"hyphen" / "dash"</span> <i class="fas fa-arrow-right cmd-arrow"></i> <span class="cmd-res">-</span></div>',
                '                    <div class="voice-cmd-tile"><span class="cmd-speech">"underscore"</span> <i class="fas fa-arrow-right cmd-arrow"></i> <span class="cmd-res">_</span></div>',
                '                    <div class="voice-cmd-tile"><span class="cmd-speech">"slash"</span> <i class="fas fa-arrow-right cmd-arrow"></i> <span class="cmd-res">/</span></div>',
                '                    <div class="voice-cmd-tile"><span class="cmd-speech">"question mark"</span> <i class="fas fa-arrow-right cmd-arrow"></i> <span class="cmd-res">?</span></div>',
                '                    <div class="voice-cmd-tile"><span class="cmd-speech">"new line"</span> <i class="fas fa-arrow-right cmd-arrow"></i> <span class="cmd-res">&#8629; (Enter)</span></div>',
                '                </div>',
                '            </div>',
                '            <div class="voice-guide-section">',
                '                <div class="voice-guide-section-title"><i class="fas fa-bolt text-primary me-1"></i> Voice Control Shortcuts</div>',
                '                <div class="voice-shortcuts-row">',
                '                    <div class="voice-shortcut-pill"><i class="fas fa-eraser text-danger me-1"></i> <strong>"clear field"</strong> &mdash; Erases entire field content</div>',
                '                    <div class="voice-shortcut-pill"><i class="fas fa-backspace text-warning me-1"></i> <strong>"delete word"</strong> &mdash; Removes the last word</div>',
                '                    <div class="voice-shortcut-pill"><i class="fas fa-i-cursor text-info me-1"></i> <strong>"select all"</strong> &mdash; Selects all text in field</div>',
                '                    <div class="voice-shortcut-pill"><i class="fas fa-stop-circle text-secondary me-1"></i> <strong>"stop listening"</strong> &mdash; Turns off microphone</div>',
                '                </div>',
                '            </div>',
                '            <div class="voice-guide-section voice-sandbox-box">',
                '                <div class="voice-guide-section-title"><i class="fas fa-flask text-success me-1"></i> Live Sandbox (Try Speaking Here)</div>',
                '                <div class="voice-sandbox-input-wrap">',
                '                    <input type="text" class="voice-sandbox-field" placeholder="Click the mic button and test voice dictation..." data-voice-mic="true">',
                '                </div>',
                '            </div>',
                '        </div>',
                '        <div class="voice-guide-modal-footer">',
                '            <button type="button" class="btn-voice-guide-close" id="btnGotItVoiceGuide">',
                '                <i class="fas fa-check me-1"></i> Got It',
                '            </button>',
                '        </div>',
                '    </div>',
                '</div>'
            ].join("");

            document.body.appendChild(modalEl);
        }

        // Event bindings for Speed Dial & Modal
        var trigger = document.getElementById("icSpeedDialTrigger");
        var speedDial = document.getElementById("icFloatingSpeedDial");
        var btnAccess = document.getElementById("icBtnAccessibility");
        var btnVoiceGuide = document.getElementById("icBtnVoiceGuide");
        var guideModal = document.getElementById("voiceInputGuideModal");
        var btnCloseModal = document.getElementById("btnCloseVoiceGuideModal");
        var btnGotIt = document.getElementById("btnGotItVoiceGuide");
        var backdrop = document.getElementById("voiceGuideBackdrop");

        if (trigger && speedDial) {
            trigger.addEventListener("click", function (e) {
                e.stopPropagation();
                speedDial.classList.toggle("open");
            });
        }

        function closeSpeedDial() {
            if (speedDial) {
                speedDial.classList.remove("open");
            }
        }

        function openVoiceGuide() {
            closeSpeedDial();
            if (guideModal) {
                guideModal.style.display = "flex";
            }
        }

        function closeVoiceGuide() {
            if (guideModal) {
                guideModal.style.display = "none";
            }
        }

        function toggleAccessibility() {
            closeSpeedDial();
            // Try triggering Sienna button
            var siennaBtn = document.querySelector(".asw-menu-btn, .asw-btn, [data-asw-btn], .sienna-accessibility-btn");
            if (siennaBtn) {
                siennaBtn.click();
                return;
            }
            if (window.sienna && typeof window.sienna.toggle === "function") {
                window.sienna.toggle();
                return;
            }
            // Load dynamically if script not loaded yet
            if (!document.querySelector('script[src*="sienna-accessibility"]')) {
                var s = document.createElement("script");
                s.src = "https://cdn.jsdelivr.net/npm/sienna-accessibility@latest/dist/sienna-accessibility.umd.js";
                s.setAttribute("data-asw-position", "bottom-right");
                s.onload = function () {
                    setTimeout(function () {
                        var btn = document.querySelector(".asw-menu-btn, .asw-btn, [data-asw-btn]");
                        if (btn) btn.click();
                    }, 350);
                };
                document.body.appendChild(s);
            }
        }

        if (btnVoiceGuide) {
            btnVoiceGuide.addEventListener("click", function (e) {
                e.stopPropagation();
                openVoiceGuide();
            });
        }

        if (btnAccess) {
            btnAccess.addEventListener("click", function (e) {
                e.stopPropagation();
                toggleAccessibility();
            });
        }

        if (btnCloseModal) {
            btnCloseModal.addEventListener("click", closeVoiceGuide);
        }

        if (btnGotIt) {
            btnGotIt.addEventListener("click", closeVoiceGuide);
        }

        if (backdrop) {
            backdrop.addEventListener("click", closeVoiceGuide);
        }

        document.addEventListener("click", function (e) {
            if (speedDial && !speedDial.contains(e.target)) {
                closeSpeedDial();
            }
        });

        document.addEventListener("keydown", function (e) {
            if (e.key === "Escape") {
                closeSpeedDial();
                closeVoiceGuide();
            }
        });
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", createSpeedDialWidget);
    } else {
        createSpeedDialWidget();
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
            "label .voice-mic-host, .dataTables_filter .voice-mic-host{display:inline-flex !important;width:auto !important;vertical-align:middle;}",
            ".voice-mic-host > input," +
                ".voice-mic-host > textarea{padding-right:44px !important;margin-bottom:0 !important;}",
            "input[type='search']::-webkit-search-cancel-button, input[type='search']::-webkit-search-decoration{display:none !important; -webkit-appearance:none !important;}",
            "input::-ms-clear, input::-ms-reveal{display:none !important; width:0 !important; height:0 !important;}",
            "." + BUTTON_CLASS + "{" +
                "position:absolute;right:12px;top:50%;transform:translateY(-50%);" +
                "width:32px;height:32px;aspect-ratio:1/1;border:none;border-radius:50%;" +
                "background:transparent;color:#ef4444;cursor:pointer;z-index:3;" +
                "display:inline-flex;align-items:center;justify-content:center;" +
                "font-size:15px;line-height:1;transition:all .2s ease;" +
                "filter:drop-shadow(0 0 4px rgba(239,68,68,0.45));}",
            "." + BUTTON_CLASS + ":hover{color:#f87171;filter:drop-shadow(0 0 6px rgba(239,68,68,0.7));transform:translateY(-50%) scale(1.15);background:rgba(239,68,68,0.12);}",
            "." + BUTTON_CLASS + "." + ACTIVE_CLASS + "{" +
                "background:#ef4444 !important;color:#ffffff !important;border-radius:50% !important;border:none !important;box-shadow:0 0 12px rgba(239,68,68,0.6) !important;}",
            "textarea + ." + BUTTON_CLASS + ", .voice-mic-host > textarea ~ ." + BUTTON_CLASS + ", textarea ~ ." + BUTTON_CLASS + ", [data-ai-insight-card] ." + BUTTON_CLASS + "{" +
                "position:absolute !important;top:10px !important;bottom:auto !important;right:12px !important;left:auto !important;margin:0 !important;transform:none !important;z-index:5 !important;}",
            "textarea + ." + BUTTON_CLASS + ":hover, .voice-mic-host > textarea ~ ." + BUTTON_CLASS + ":hover, textarea ~ ." + BUTTON_CLASS + ":hover, [data-ai-insight-card] ." + BUTTON_CLASS + ":hover{" +
                "position:absolute !important;top:10px !important;bottom:auto !important;right:12px !important;left:auto !important;margin:0 !important;transform:scale(1.12) !important;}"
        ].join("");

        document.head.appendChild(style);
    }

    function isEligibleField(field) {
        if (!field || field.dataset.voiceMicSkip === "true") {
            return false;
        }

        if (field.classList.contains("voice-sandbox-field") || field.dataset.voiceMic === "true") {
            return true;
        }

        if (field.closest(".swal2-container, .swal2-popup, #voiceInputGuideModal")) {
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
        if (host && (host.classList.contains("input-wrap") || host.classList.contains("input-group"))) {
            // Already inside a positioned wrapper, do not insert extra wrapper element
        } else if (!host.classList.contains("voice-mic-host")) {
            var wrapper = document.createElement("div");
            wrapper.className = "voice-mic-host";
            var computedMb = window.getComputedStyle(field).marginBottom;
            if (computedMb && computedMb !== "0px") {
                wrapper.style.marginBottom = computedMb;
            }
            host.insertBefore(wrapper, field);
            wrapper.appendChild(field);
            host = wrapper;
        }

        var button = document.createElement("button");
        button.type = "button";
        button.className = BUTTON_CLASS;
        button.setAttribute("aria-label", "Start voice input");
        button.setAttribute("title", "Start voice input");
        button.innerHTML = '<i class="fa fa-microphone"></i>';

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
        if (scope instanceof Element && scope.closest && scope.closest(".swal2-container, .swal2-popup, #voiceInputGuideModal")) {
            return;
        }
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

                    if (node.matches && node.matches(".swal2-container, .swal2-popup, #voiceInputGuideModal")) {
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
