/**
 * InternConnect AI Chatbot Assistant & System Navigator Widget
 */
(function () {
    "use strict";

    var CHAT_ENDPOINT = "/chatbot/message";
    var STORAGE_KEY = "internconnect_chatbot_history";

    var ChatbotWidget = {
        isOpen: false,
        isLoading: false,
        recognition: null,
        isListening: false,
        history: [],

        detectUserRole: function () {
            var path = (window.location.pathname || "").toLowerCase();

            // 1. Check DOM badge / user role text
            var userRoleEl = document.querySelector(".user-role, .topbar-badge, [data-user-role]");
            var roleText = userRoleEl ? (userRoleEl.textContent || "").toLowerCase() : "";

            if (roleText.includes("coordinator") || roleText.includes("admin")) {
                return "coordinator";
            }
            if (roleText.includes("professor") || roleText.includes("faculty")) {
                return "professor";
            }
            if (roleText.includes("student")) {
                return "student";
            }

            // 2. Auth routes
            if (path.includes("login") || path.includes("onboarding") || path.includes("forgot") || path.includes("reset") || path.includes("registration") || path.includes("gateway")) {
                return "auth";
            }

            // 3. Landing page
            if (path === "/" || path.endsWith("/landing") || path.includes("landing")) {
                return "landing";
            }

            // 4. Coordinator specific URL paths
            var coordPaths = [
                "studentlists", "students", "professorstab", "uploadpage", "upload", 
                "maintenance", "moa", "reports", "reportsexpired", "reportst", 
                "audit", "audit_log", "accountinfo", "moa-unlock-requests", "moaview",
                "coordinator"
            ];
            if (coordPaths.some(function (p) { return path.includes(p); })) {
                return "coordinator";
            }

            // 5. Professor specific URL paths
            var profPaths = [
                "homeprof", "liststudents", "classprof", "classlist", "class", 
                "requirementstatus", "requirementstatusclasses", "evaluationprof", 
                "expiredmoareportsprof", "analyticsprof", "profacc", "studentrequire", 
                "filecategory", "allstudents", "requireview", "prof"
            ];
            if (profPaths.some(function (p) { return path.includes(p); })) {
                return "professor";
            }

            // 6. Student specific URL paths
            var studentPaths = [
                "student_home", "studenthome", "filereq", "student_file", "student_class", 
                "evaluation", "companiesup", "ojtinfo", "student_account", "pending"
            ];
            if (studentPaths.some(function (p) { return path.includes(p); })) {
                return "student";
            }

            if (path.includes("dashboard")) {
                return "coordinator";
            }

            return "student";
        },

        getRoleConfig: function (role) {
            role = role || this.detectUserRole();
            switch (role) {
                case "coordinator":
                    return {
                        welcome: "Hi! My name is **Bud**, your OJT Buddy! 🐾 Welcome, **OJT Coordinator**! I can help you manage partner companies, track MOA expirations, sync faculty and student rosters, and monitor overall OJT compliance.\n\nHow can I help you today?",
                        placeholder: "Ask Bud about companies, MOA, sync, or compliance...",
                        suggestions: [
                            "How to sync faculty from FLSS?",
                            "Where do I manage partner companies and MOA?",
                            "How to sync students from GuiSIS?",
                            "How to sync degree programs from PUPTAS?",
                            "Where are the expired MOA reports?",
                            "How to handle MOA unlock requests?"
                        ]
                    };
                case "professor":
                    return {
                        welcome: "Hi! My name is **Bud**, your OJT Buddy! 🐾 Welcome, **Professor**! I can help you review student requirement submissions, check supervisor evaluation forms, manage class sections, and track internship progress.\n\nHow can I help you today?",
                        placeholder: "Ask Bud about submissions, sections, evaluations...",
                        suggestions: [
                            "How do I review student requirement submissions?",
                            "Where can I see student supervisor evaluations?",
                            "How to view class adviser analytics?",
                            "Where do I view student masterlist?",
                            "Where are expired MOA records?"
                        ]
                    };
                case "auth":
                    return {
                        welcome: "Hi! My name is **Bud**, your OJT Buddy! 🐾 Welcome to the **InternConnect Sign-In Portal**! Need help choosing a portal, using IdP, or signing in with local credentials?\n\nHow can I help you today?",
                        placeholder: "Ask Bud about IDP, sign-in, or portals...",
                        suggestions: [
                            "What is IDP?",
                            "IDP is down, what should I do?",
                            "What are Local Credentials?",
                            "Which portal should I choose?"
                        ]
                    };
                case "landing":
                    return {
                        welcome: "Hi! My name is **Bud**, your OJT Buddy! 🐾 Welcome to **InternConnect**! I can help you navigate the landing page, show you how to launch the portal, or answer questions about the system.\n\nHow can I help you today?",
                        placeholder: "Ask Bud about InternConnect, portals, etc...",
                        suggestions: [
                            "How do I go to the main website?",
                            "What is InternConnect?",
                            "What is IDP?",
                            "How do I contact support?"
                        ]
                    };
                case "student":
                default:
                    return {
                        welcome: "Hi! My name is **Bud**, your OJT Buddy! 🐾 I can help you navigate InternConnect, understand your OJT requirement phases, submit documents, and manage your internship workflow.\n\nHow can I help you today?",
                        placeholder: "Ask Bud about navigation, requirements, etc...",
                        suggestions: [
                            "Explain the requirement phases",
                            "Where do I submit my Notarized MOA?",
                            "Where do I upload requirements?",
                            "Where can I update my OJT Information?",
                            "How do I sync my profile with GuiSIS?",
                            "How does supervisor evaluation work?"
                        ]
                    };
            }
        },

        detectUserIdentity: function () {
            var userNameEl = document.querySelector(".user-name, [data-user-name]");
            var name = userNameEl ? (userNameEl.textContent || "").trim() : "";
            var role = this.detectUserRole();
            if (name) {
                var slug = name.toLowerCase().replace(/[^a-z0-9]+/g, "_");
                return slug + "_" + role;
            }
            return role;
        },

        getStorageKey: function () {
            return STORAGE_KEY + "_" + this.detectUserIdentity();
        },

        init: function () {
            if (document.getElementById("icChatbotDrawer")) {
                return;
            }

            this.injectHTML();
            this.bindEvents();
            this.loadHistory();
            this.initSpeech();
        },

        injectHTML: function () {
            var self = this;
            var config = this.getRoleConfig();

            var drawer = document.createElement("div");
            drawer.className = "ic-chatbot-drawer";
            drawer.id = "icChatbotDrawer";
            drawer.innerHTML = [
                '<div class="ic-chat-header">',
                '    <div class="ic-chat-header-info">',
                '        <div class="ic-chat-avatar-wrap">',
                '            <div class="ic-bud-drawer-frame" id="icDrawerBudAvatar">',
                '                <div class="ic-bud-drawer-circle"></div>',
                '                <div class="ic-bud-drawer-popout">',
                '                    <img src="/images/mascot/bud_happy_wave.png" alt="Bud" class="ic-bud-drawer-img">',
                '                </div>',
                '            </div>',
                '            <div class="ic-chat-status-dot"></div>',
                '        </div>',
                '        <div class="ic-chat-title-group">',
                '            <h5>Bud <span class="ic-badge-buddy">Your OJT Buddy</span></h5>',
                '            <p>System Navigator & Support</p>',
                '        </div>',
                '    </div>',
                '    <div class="ic-chat-header-actions">',
                '        <button type="button" class="ic-chat-btn-icon" id="icBtnClearChat" title="Clear Conversation"><i class="fas fa-trash-alt"></i></button>',
                '        <button type="button" class="ic-chat-btn-icon" id="icBtnCloseChat" title="Close Assistant"><i class="fas fa-times"></i></button>',
                '    </div>',
                '</div>',
                '<div class="ic-chat-privacy-banner">',
                '    <i class="fas fa-shield-alt"></i>',
                '    <span>Privacy Protected &bull; System navigation & OJT guide</span>',
                '</div>',
                '<div class="ic-chat-body" id="icChatBody">',
                '</div>',
                '<div class="ic-chat-suggestions-wrap" id="icChatSuggestionsWrap">',
                '    <button type="button" class="ic-sug-nav-btn prev" id="icSugNavPrev" title="Scroll left" aria-label="Scroll left"><i class="fas fa-chevron-left"></i></button>',
                '    <div class="ic-chat-suggestions" id="icChatSuggestions">',
                config.suggestions.map(function(sug) {
                    return '    <button type="button" class="ic-chat-chip" data-query="' + sug + '">' + sug + '</button>';
                }).join('\n'),
                '    </div>',
                '    <button type="button" class="ic-sug-nav-btn next" id="icSugNavNext" title="Scroll right" aria-label="Scroll right"><i class="fas fa-chevron-right"></i></button>',
                '</div>',
                '<div class="ic-chat-footer">',
                '    <div class="ic-chat-input-box">',
                '        <input type="text" class="ic-chat-input" id="icChatInput" placeholder="' + config.placeholder + '" autocomplete="off">',
                '        <button type="button" class="ic-chat-mic-btn" id="icChatMicBtn" title="Speak to Dictate"><i class="fas fa-microphone"></i></button>',
                '    </div>',
                '    <button type="button" class="ic-chat-send-btn" id="icChatSendBtn" title="Send Message to Bud"><i class="fas fa-paper-plane"></i></button>',
                '</div>',
                '<div class="ic-chat-splash" id="icChatSplash">',
                '    <div class="ic-splash-particles">',
                '        <span class="ic-splash-sparkle s1">✨</span>',
                '        <span class="ic-splash-sparkle s2">⭐</span>',
                '        <span class="ic-splash-sparkle s3">🐾</span>',
                '        <span class="ic-splash-sparkle s4">✨</span>',
                '    </div>',
                '    <div class="ic-splash-content">',
                '        <div class="ic-splash-mascot-wrap">',
                '            <div class="ic-splash-pulse-ring"></div>',
                '            <div class="ic-splash-pulse-ring delay"></div>',
                '            <div class="ic-splash-avatar-circle">',
                '                <img src="/images/mascot/bud_happy_wave.png" alt="Bud Mascot" class="ic-splash-avatar-img">',
                '            </div>',
                '            <div class="ic-splash-badge">🐾 OJT Buddy</div>',
                '        </div>',
                '        <div class="ic-splash-text">',
                '            <h4 class="ic-splash-title">Bud is waking up...</h4>',
                '            <p class="ic-splash-subtitle" id="icSplashSubtitle">Getting ready to help you!</p>',
                '        </div>',
                '        <div class="ic-splash-loader">',
                '            <div class="ic-splash-loader-bar">',
                '                <div class="ic-splash-loader-progress"></div>',
                '            </div>',
                '            <div class="ic-splash-paws">',
                '                <span class="ic-paw-dot">🐾</span>',
                '                <span class="ic-paw-dot">🐾</span>',
                '                <span class="ic-paw-dot">🐾</span>',
                '            </div>',
                '        </div>',
                '        <div class="ic-splash-skip-hint">Click anywhere to skip</div>',
                '    </div>',
                '</div>'
            ].join("");

            document.body.appendChild(drawer);
        },

        bindEvents: function () {
            var self = this;
            var btnClose = document.getElementById("icBtnCloseChat");
            var btnClear = document.getElementById("icBtnClearChat");
            var btnSend = document.getElementById("icChatSendBtn");
            var input = document.getElementById("icChatInput");
            var suggestions = document.getElementById("icChatSuggestions");
            var btnPrev = document.getElementById("icSugNavPrev");
            var btnNext = document.getElementById("icSugNavNext");
            var micBtn = document.getElementById("icChatMicBtn");

            if (btnClose) {
                btnClose.addEventListener("click", function () {
                    self.close();
                });
            }

            if (btnClear) {
                btnClear.addEventListener("click", function () {
                    self.clearChat();
                });
            }

            if (btnSend) {
                btnSend.addEventListener("click", function () {
                    self.sendCurrentMessage();
                });
            }

            if (input) {
                input.addEventListener("keydown", function (e) {
                    if (e.key === "Enter" && !e.shiftKey) {
                        e.preventDefault();
                        self.sendCurrentMessage();
                    }
                });
            }

            var wrap = document.getElementById("icChatSuggestionsWrap");
            var isDown = false;
            var startX = 0;
            var scrollLeft = 0;
            var isDragging = false;
            var suppressChipClick = false;

            // Desktop & Mobile Prev / Next scroll buttons
            if (btnPrev && suggestions) {
                btnPrev.addEventListener("click", function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    suggestions.scrollBy({ left: -200, behavior: "smooth" });
                    setTimeout(function () { self.updateSuggestionNavState(); }, 220);
                });
            }
            if (btnNext && suggestions) {
                btnNext.addEventListener("click", function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    suggestions.scrollBy({ left: 200, behavior: "smooth" });
                    setTimeout(function () { self.updateSuggestionNavState(); }, 220);
                });
            }

            // Mouse wheel horizontal scrolling over suggestions wrap
            var handleWheel = function (e) {
                var delta = e.deltaY !== 0 ? e.deltaY : e.deltaX;
                if (delta !== 0) {
                    e.preventDefault();
                    suggestions.scrollLeft += delta * 1.2;
                    self.updateSuggestionNavState();
                }
            };

            if (wrap) {
                wrap.addEventListener("wheel", handleWheel, { passive: false });
                wrap.addEventListener("mouseenter", function () {
                    self.updateSuggestionNavState();
                });
            } else if (suggestions) {
                suggestions.addEventListener("wheel", handleWheel, { passive: false });
            }

            if (suggestions) {
                suggestions.addEventListener("scroll", function () {
                    self.updateSuggestionNavState();
                }, { passive: true });

                // Touch swipe handlers for mobile
                var touchStartX = 0;
                var touchScrollLeft = 0;
                var isTouching = false;

                suggestions.addEventListener("touchstart", function (e) {
                    if (e.touches.length === 1) {
                        isTouching = true;
                        touchStartX = e.touches[0].pageX;
                        touchScrollLeft = suggestions.scrollLeft;
                    }
                }, { passive: true });

                suggestions.addEventListener("touchmove", function (e) {
                    if (!isTouching || e.touches.length !== 1) return;
                    var diffX = e.touches[0].pageX - touchStartX;
                    if (Math.abs(diffX) > 6) {
                        suppressChipClick = true;
                    }
                }, { passive: true });

                suggestions.addEventListener("touchend", function () {
                    isTouching = false;
                    setTimeout(function () {
                        suppressChipClick = false;
                        self.updateSuggestionNavState();
                    }, 80);
                });

                // Mouse drag-to-scroll
                suggestions.addEventListener("mousedown", function (e) {
                    if (e.button !== 0) return; // Only left click
                    isDown = true;
                    isDragging = false;
                    startX = e.pageX - suggestions.offsetLeft;
                    scrollLeft = suggestions.scrollLeft;
                });

                window.addEventListener("mouseup", function () {
                    if (!isDown) return;
                    isDown = false;
                    suggestions.classList.remove("dragging");
                    if (isDragging) {
                        suppressChipClick = true;
                        setTimeout(function () {
                            suppressChipClick = false;
                            isDragging = false;
                        }, 120);
                    }
                });

                window.addEventListener("mousemove", function (e) {
                    if (!isDown) return;
                    var x = e.pageX - suggestions.offsetLeft;
                    var walk = (x - startX);
                    if (Math.abs(walk) > 4) {
                        isDragging = true;
                        suggestions.classList.add("dragging");
                        e.preventDefault();
                        suggestions.scrollLeft = scrollLeft - walk;
                        self.updateSuggestionNavState();
                    }
                });
            }

            // Delegated click handler for all suggestion chips across suggestions bar or message body
            document.addEventListener("click", function (e) {
                if (suppressChipClick) {
                    e.preventDefault();
                    e.stopPropagation();
                    return;
                }
                var chip = e.target.closest(".ic-chat-chip");
                if (chip && chip.dataset.query) {
                    e.preventDefault();
                    self.sendMessage(chip.dataset.query);
                }
            }, true);

            window.addEventListener("resize", function () {
                if (self.isOpen) {
                    self.updateSuggestionNavState();
                }
            });

            if (micBtn) {
                micBtn.addEventListener("click", function () {
                    self.toggleSpeech();
                });
            }

            document.addEventListener("keydown", function (e) {
                if (e.key === "Escape" && self.isOpen) {
                    self.close();
                }
            });
        },

        initSpeech: function () {
            var SpeechClass = window.SpeechRecognition || window.webkitSpeechRecognition;
            if (!SpeechClass) {
                var mic = document.getElementById("icChatMicBtn");
                if (mic) mic.style.display = "none";
                return;
            }

            var self = this;
            try {
                this.recognition = new SpeechClass();
                this.recognition.continuous = false;
                this.recognition.interimResults = false;
                this.recognition.lang = "en-US";

                this.recognition.onstart = function () {
                    self.isListening = true;
                    var mic = document.getElementById("icChatMicBtn");
                    if (mic) mic.classList.add("listening");
                };

                this.recognition.onresult = function (event) {
                    var transcript = event.results[0][0].transcript;
                    var input = document.getElementById("icChatInput");
                    if (input && transcript) {
                        input.value = (input.value ? input.value + " " : "") + transcript;
                        input.focus();
                    }
                };

                this.recognition.onerror = function () {
                    self.isListening = false;
                    var mic = document.getElementById("icChatMicBtn");
                    if (mic) mic.classList.remove("listening");
                };

                this.recognition.onend = function () {
                    self.isListening = false;
                    var mic = document.getElementById("icChatMicBtn");
                    if (mic) mic.classList.remove("listening");
                };
            } catch (e) {
                console.warn("Speech recognition not supported in this browser context", e);
            }
        },

        toggleSpeech: function () {
            if (!this.recognition) return;
            if (this.isListening) {
                this.recognition.stop();
            } else {
                try {
                    this.recognition.start();
                } catch (e) {
                    console.warn(e);
                }
            }
        },

        playOpeningAnimation: function () {
            var splash = document.getElementById("icChatSplash");
            var subtitle = document.getElementById("icSplashSubtitle");
            if (!splash) return;

            splash.classList.remove("fade-out");
            splash.classList.add("active");
            if (subtitle) {
                subtitle.textContent = "Getting ready to help you...";
            }

            var self = this;
            var dismissed = false;

            function dismissSplash() {
                if (dismissed) return;
                dismissed = true;
                splash.classList.add("fade-out");
                setTimeout(function () {
                    splash.classList.remove("active");
                    splash.classList.remove("fade-out");
                    var input = document.getElementById("icChatInput");
                    if (input && self.isOpen) {
                        input.focus();
                    }
                    self.scrollToBottom();
                }, 260);
            }

            // Interactive skip on click
            splash.onclick = function () {
                dismissSplash();
            };

            // Text transition tick
            setTimeout(function () {
                if (!dismissed && subtitle) {
                    subtitle.textContent = "Wagging tail & loading navigation...";
                }
            }, 420);

            // Auto-dismiss smoothly after 850ms
            setTimeout(function () {
                dismissSplash();
            }, 880);
        },

        open: function () {
            if (!document.getElementById("icChatbotDrawer")) {
                this.init();
            }
            var drawer = document.getElementById("icChatbotDrawer");
            if (drawer) {
                drawer.classList.add("open");
                this.isOpen = true;
                this.playOpeningAnimation();
                this.scrollToBottom();
                var self = this;
                [80, 250, 450, 750].forEach(function (t) {
                    setTimeout(function () {
                        self.updateSuggestionNavState();
                    }, t);
                });
            }
        },

        close: function () {
            var drawer = document.getElementById("icChatbotDrawer");
            if (drawer) {
                drawer.classList.remove("open");
                this.isOpen = false;
            }
        },

        toggle: function () {
            if (this.isOpen) {
                this.close();
            } else {
                this.open();
            }
        },

        loadHistory: function () {
            // Purge legacy un-scoped session storage key from previous versions
            try {
                if (sessionStorage.getItem(STORAGE_KEY)) {
                    sessionStorage.removeItem(STORAGE_KEY);
                }
            } catch (e) {}

            var key = this.getStorageKey();
            try {
                var stored = sessionStorage.getItem(key);
                if (stored) {
                    this.history = JSON.parse(stored);
                } else {
                    this.history = [];
                }
            } catch (e) {
                this.history = [];
            }

            if (!this.history || this.history.length === 0) {
                var config = this.getRoleConfig();
                this.addBotMessage({
                    reply: config.welcome,
                    actions: [],
                    suggestions: config.suggestions
                }, false);
            } else {
                var self = this;
                this.history.forEach(function (msg) {
                    if (msg.type === "user") {
                        self.renderUserBubble(msg.text);
                    } else {
                        self.renderBotBubble(msg.data);
                    }
                });
            }
        },

        saveHistory: function () {
            try {
                sessionStorage.setItem(this.getStorageKey(), JSON.stringify(this.history.slice(-20)));
            } catch (e) {}
        },

        clearChat: function () {
            this.isLoading = false;
            this.setLoading(false);
            this.history = [];
            sessionStorage.removeItem(this.getStorageKey());
            var body = document.getElementById("icChatBody");
            if (body) body.innerHTML = "";
            this.loadHistory();
        },

        sendCurrentMessage: function () {
            var input = document.getElementById("icChatInput");
            if (!input) return;
            var text = input.value.trim();
            if (text === "" || this.isLoading) return;

            input.value = "";
            this.sendMessage(text);
        },

        sendMessage: function (text) {
            if (!text || this.isLoading) return;

            this.addUserMessage(text);
            this.setLoading(true);

            var self = this;
            var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

            fetch(CHAT_ENDPOINT, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    message: text,
                    role: self.detectUserRole(),
                    history: self.history.slice(-6)
                })
            })
            .then(function (res) {
                return res.json();
            })
            .then(function (data) {
                self.setLoading(false);
                if (data && data.success) {
                    self.addBotMessage(data);
                } else {
                    self.addBotMessage({
                        reply: "I am having trouble connecting right now. Please reach out to our OJT Coordinators directly.",
                        escalate: true,
                        support: {
                            email: "internconnect.ojtims@gmail.com",
                            facebook: "https://www.facebook.com/profile.php?id=61593939354633"
                        }
                    });
                }
            })
            .catch(function (err) {
                self.setLoading(false);
                console.error("Chatbot error", err);
                self.addBotMessage({
                    reply: "Network connection error. If you need urgent assistance, you can message our official Facebook page or email the OJT coordinator.",
                    escalate: true,
                    support: {
                        email: "internconnect.ojtims@gmail.com",
                        facebook: "https://www.facebook.com/profile.php?id=61593939354633"
                    }
                });
            });
        },

        addUserMessage: function (text) {
            this.history.push({ type: "user", text: text });
            this.saveHistory();
            this.renderUserBubble(text);
            this.scrollToBottom();
        },

        renderUserBubble: function (text) {
            var body = document.getElementById("icChatBody");
            if (!body) return;

            var row = document.createElement("div");
            row.className = "ic-msg-row user";
            row.innerHTML = '<div class="ic-msg-bubble">' + this.escapeHtml(text) + '</div>';
            body.appendChild(row);
        },

        addBotMessage: function (data, save) {
            if (save !== false) {
                this.history.push({ type: "bot", data: data });
                this.saveHistory();
            }
            this.renderBotBubble(data);
            this.updateSuggestions(data.suggestions || []);
            this.scrollToBottom();
        },

        renderBotBubble: function (data) {
            var body = document.getElementById("icChatBody");
            if (!body) return;

            var replyHtml = this.formatMarkdown(data.reply || "");

            // Action navigation buttons
            var actionsHtml = "";
            if (data.actions && data.actions.length > 0) {
                var filteredActions = data.escalate
                    ? data.actions.filter(function(act) {
                        return !act.url.includes("facebook.com") && !act.url.startsWith("mailto:");
                    })
                    : data.actions;

                if (filteredActions.length > 0) {
                    actionsHtml = '<div class="ic-chat-actions-wrap">';
                    filteredActions.forEach(function (act) {
                        var targetAttr = act.external ? ' target="_blank" rel="noopener noreferrer"' : '';
                        var iconTag = act.icon ? '<i class="' + act.icon + '"></i> ' : '<i class="fas fa-external-link-alt"></i> ';
                        actionsHtml += '<a href="' + act.url + '" class="ic-chat-action-btn"' + targetAttr + '>' +
                            '<span>' + iconTag + act.label + '</span>' +
                            '<i class="fas fa-chevron-right action-arrow"></i>' +
                            '</a>';
                    });
                    actionsHtml += '</div>';
                }
            }

            // Support escalation card
            var supportHtml = "";
            if (data.escalate) {
                var email = (data.support && data.support.email) ? data.support.email : "internconnect.ojtims@gmail.com";
                var fb = (data.support && data.support.facebook) ? data.support.facebook : "https://www.facebook.com/profile.php?id=61593939354633";

                supportHtml = [
                    '<div class="ic-chat-support-card">',
                    '    <div class="ic-support-title"><i class="fas fa-headset"></i> Need Direct Coordinator Assistance?</div>',
                    '    <div class="ic-support-btn-group">',
                    '        <a href="' + fb + '" target="_blank" rel="noopener noreferrer" class="ic-support-btn fb"><i class="fab fa-facebook-messenger"></i> Message on Facebook</a>',
                    '        <a href="mailto:' + email + '" class="ic-support-btn email"><i class="fas fa-envelope"></i> Send Email (' + email + ')</a>',
                    '    </div>',
                    '</div>'
                ].join("");
            }

            var botAvatar = '/images/mascot/bud_happy_wave.png';
            if (data.errorAvatar) {
                botAvatar = data.errorAvatar;
            } else if (data.escalate) {
                botAvatar = '/images/mascot/bud_apologetic.png';
            } else if (data.actions && data.actions.length > 0) {
                botAvatar = '/images/mascot/bud_pointing.png';
            } else if (data.celebrate) {
                botAvatar = '/images/mascot/bud_celebrate.png';
            } else {
                botAvatar = '/images/mascot/bud_thumbs_up.png';
            }

            var headerImg = document.querySelector("#icDrawerBudAvatar img");
            if (headerImg) headerImg.src = botAvatar;

            var row = document.createElement("div");
            row.className = "ic-msg-row bot";
            row.innerHTML = [
                '<div class="ic-msg-avatar">',
                '    <div class="ic-bud-msg-circle"></div>',
                '    <div class="ic-bud-msg-popout">',
                '        <img src="' + botAvatar + '" alt="Bud" class="ic-bud-msg-icon">',
                '    </div>',
                '</div>',
                '<div class="ic-msg-bubble">',
                replyHtml,
                actionsHtml,
                supportHtml,
                '</div>'
            ].join("");

            body.appendChild(row);
        },

        updateSuggestions: function (suggestions) {
            var box = document.getElementById("icChatSuggestions");
            var wrap = document.getElementById("icChatSuggestionsWrap");
            if (!box) return;

            if (!suggestions || suggestions.length === 0) {
                if (wrap) wrap.style.display = "none";
                else box.style.display = "none";
                return;
            }

            if (wrap) wrap.style.display = "flex";
            box.style.display = "flex";
            box.innerHTML = "";
            suggestions.forEach(function (sug) {
                var btn = document.createElement("button");
                btn.type = "button";
                btn.className = "ic-chat-chip";
                btn.dataset.query = sug;
                btn.textContent = sug;
                box.appendChild(btn);
            });

            var self = this;
            setTimeout(function () {
                box.scrollLeft = 0;
                self.updateSuggestionNavState();
            }, 60);
        },

        updateSuggestionNavState: function () {
            var box = document.getElementById("icChatSuggestions");
            var btnPrev = document.getElementById("icSugNavPrev");
            var btnNext = document.getElementById("icSugNavNext");
            if (!box || !btnPrev || !btnNext) return;

            var scrollLeft = Math.ceil(box.scrollLeft);
            var maxScroll = Math.floor(box.scrollWidth - box.clientWidth);

            if (maxScroll <= 2) {
                btnPrev.classList.remove("visible");
                btnNext.classList.remove("visible");
                return;
            }

            if (scrollLeft > 6) {
                btnPrev.classList.add("visible");
            } else {
                btnPrev.classList.remove("visible");
            }

            if (scrollLeft < maxScroll - 6) {
                btnNext.classList.add("visible");
            } else {
                btnNext.classList.remove("visible");
            }
        },

        setLoading: function (loading) {
            this.isLoading = loading;
            var body = document.getElementById("icChatBody");
            var btnSend = document.getElementById("icChatSendBtn");
            var headerImg = document.querySelector("#icDrawerBudAvatar img");

            if (btnSend) btnSend.disabled = loading;

            var existing = document.getElementById("icTypingIndicator");
            if (existing) existing.remove();

            if (loading) {
                if (headerImg) headerImg.src = '/images/mascot/bud_thinking.png';
                if (body) {
                    var indicator = document.createElement("div");
                    indicator.className = "ic-msg-row bot";
                    indicator.id = "icTypingIndicator";
                    indicator.innerHTML = [
                        '<div class="ic-msg-avatar">',
                        '    <div class="ic-bud-msg-circle"></div>',
                        '    <div class="ic-bud-msg-popout">',
                        '        <img src="/images/mascot/bud_thinking.png" alt="Bud" class="ic-bud-msg-icon">',
                        '    </div>',
                        '</div>',
                        '<div class="ic-typing-indicator">',
                        '    <span class="ic-typing-label">Bud is thinking</span>',
                        '    <div class="ic-typing-dot"></div>',
                        '    <div class="ic-typing-dot"></div>',
                        '    <div class="ic-typing-dot"></div>',
                        '</div>'
                    ].join("");
                    body.appendChild(indicator);
                    this.scrollToBottom();
                }
            }
        },

        scrollToBottom: function () {
            var body = document.getElementById("icChatBody");
            if (body) {
                setTimeout(function () {
                    body.scrollTop = body.scrollHeight;
                }, 50);
            }
        },

        formatMarkdown: function (text) {
            if (!text) return "";
            var html = this.escapeHtml(text);

            // Bold **text**
            html = html.replace(/\*\*(.*?)\*\*/g, "<strong>$1</strong>");

            // Bullet points
            html = html.replace(/^\s*-\s+(.*)$/gm, "<li>$1</li>");
            html = html.replace(/(<li>.*<\/li>)/s, "<ul>$1</ul>");

            // Newlines
            html = html.replace(/\n\n/g, "</p><p>");
            html = html.replace(/\n/g, "<br>");

            return "<p>" + html + "</p>";
        },

        escapeHtml: function (str) {
            if (!str) return "";
            return str
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    };

    window.ChatbotWidget = ChatbotWidget;

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", function () {
            ChatbotWidget.init();
        });
    } else {
        ChatbotWidget.init();
    }
})();
