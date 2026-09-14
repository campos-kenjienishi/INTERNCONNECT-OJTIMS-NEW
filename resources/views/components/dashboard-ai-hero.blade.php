@props(['role' => 'student'])

@php
    $role = strtolower($role);
    $roleLabel = match($role) {
        'student', '0', '3' => 'Student Assistant',
        'professor', 'faculty', '2' => 'Faculty Assistant',
        'coordinator', 'admin', '1' => 'Coordinator Assistant',
        default => 'System Assistant'
    };

    $suggestions = match($role) {
        'student', '0', '3' => [
            ['q' => 'Explain the requirement phases', 'label' => 'Requirement Phases', 'icon' => 'fas fa-tasks'],
            ['q' => 'Where do I submit my Notarized MOA?', 'label' => 'Submit Notarized MOA', 'icon' => 'fas fa-file-contract'],
            ['q' => 'Where do I upload my OJT requirements?', 'label' => 'Upload Requirements', 'icon' => 'fas fa-cloud-upload-alt'],
            ['q' => 'Where can I update my OJT Information?', 'label' => 'OJT Information', 'icon' => 'fas fa-layer-group'],
            ['q' => 'How do I sync my profile with GuiSIS?', 'label' => 'Sync GuiSIS Profile', 'icon' => 'fas fa-sync-alt'],
            ['q' => 'How does supervisor evaluation work?', 'label' => 'Supervisor Evaluation', 'icon' => 'fas fa-star-half-alt'],
        ],
        'professor', 'faculty', '2' => [
            ['q' => 'How do I review student requirement submissions?', 'label' => 'Review Section Requirements', 'icon' => 'fas fa-check-circle'],
            ['q' => 'Where can I see student supervisor evaluations?', 'label' => 'Student Evaluations', 'icon' => 'fas fa-star-half-alt'],
            ['q' => 'How to view class adviser analytics?', 'label' => 'Adviser Class Analytics', 'icon' => 'fas fa-chart-line'],
        ],
        'coordinator', 'admin', '1' => [
            ['q' => 'How to sync faculty professors from FLSS?', 'label' => 'Faculty FLSS Sync', 'icon' => 'fas fa-chalkboard-teacher'],
            ['q' => 'Where do I manage partner company MOAs?', 'label' => 'Partner Companies & MOAs', 'icon' => 'fas fa-building'],
            ['q' => 'How to sync students from GuiSIS pool?', 'label' => 'Student GuiSIS Sync', 'icon' => 'fas fa-sync-alt'],
            ['q' => 'Where are the expired MOA reports?', 'label' => 'Expired MOA Masterlist', 'icon' => 'fas fa-file-excel'],
        ],
        default => [
            ['q' => 'What are the general OJT requirements?', 'label' => 'OJT Guidelines', 'icon' => 'fas fa-info-circle'],
            ['q' => 'How to contact OJT Coordinators?', 'label' => 'Contact Support', 'icon' => 'fas fa-headset'],
        ]
    };
@endphp

<div class="panel-card ic-ai-vertical-card" id="icEmbeddedAiCard" data-role="{{ $role }}">
    <div class="panel-card-header ic-ai-card-header">
        <div class="ic-ai-header-left">
            <div class="ic-bud-avatar-frame" id="icBudHeaderAvatar">
                <div class="ic-bud-circle-bg"></div>
                <div class="ic-bud-popout-wrap">
                    <img src="{{ vasset('images/mascot/bud_happy_wave.png') }}" alt="Bud - Your OJT Buddy" class="ic-bud-img">
                </div>
            </div>
            <div class="ic-ai-header-text">
                <div class="d-flex align-items-center gap-2">
                    <h3 class="ic-ai-card-title">Bud</h3>
                    <span class="ic-ai-badge-role">{{ $roleLabel }}</span>
                </div>
                <p class="ic-ai-card-desc"><i class="fas fa-sparkles text-warning me-1"></i> Your OJT Buddy & Guide</p>
            </div>
        </div>
        <div class="ic-ai-header-actions">
            <button type="button" class="ic-ai-btn-sm-icon" id="icBtnClearEmbeddedChat" title="Reset Chat Conversation">
                <i class="fas fa-redo-alt"></i>
            </button>
        </div>
    </div>

    <!-- In-place Embedded Chat Body -->
    <div class="ic-ai-card-body ic-embedded-chat-body" id="icEmbeddedChatBody">
        <div class="ic-embedded-msg bot">
            <div class="ic-embedded-msg-avatar">
                <div class="ic-bud-msg-circle"></div>
                <div class="ic-bud-msg-popout">
                    <img src="{{ vasset('images/mascot/bud_happy_wave.png') }}" alt="Bud" class="ic-bud-msg-icon">
                </div>
            </div>
            <div class="ic-embedded-msg-content">
                <p>Hi! My name is <strong>Bud</strong>, your OJT Buddy! 🐾 How can I help you navigate InternConnect today?</p>
            </div>
        </div>

        <div class="ic-embedded-suggestions" id="icEmbeddedSuggestions">
            @foreach($suggestions as $sug)
                <button type="button" class="ic-embedded-chip" data-query="{{ $sug['q'] }}">
                    <span class="ic-chip-icon"><i class="{{ $sug['icon'] }}"></i></span>
                    <span class="ic-chip-text">{{ $sug['label'] }}</span>
                    <i class="fas fa-arrow-right ic-chip-arrow"></i>
                </button>
            @endforeach
        </div>
    </div>

    <!-- Embedded Chat Footer Input -->
    <div class="ic-embedded-footer">
        <div class="ic-embedded-input-wrap">
            <input type="text" class="ic-embedded-input" id="icEmbeddedInput" data-voice-mic-skip="true" placeholder="Ask Bud about requirements, navigation, etc..." autocomplete="off">
            <button type="button" class="ic-embedded-send-btn" id="icEmbeddedSendBtn" title="Send Question to Bud">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

<script>
(function () {
    "use strict";

    var ENDPOINT = "/chatbot/message";
    var BUD_IMG_WAVING = "{{ vasset('images/mascot/bud_waving.png') }}";
    var BUD_IMG_HAPPY_WAVE = "{{ vasset('images/mascot/bud_happy_wave.png') }}";
    var BUD_IMG_POINTING = "{{ vasset('images/mascot/bud_pointing.png') }}";
    var BUD_IMG_THINKING = "{{ vasset('images/mascot/bud_thinking.png') }}";
    var BUD_IMG_THUMBS_UP = "{{ vasset('images/mascot/bud_thumbs_up.png') }}";
    var BUD_IMG_CHEERING = "{{ vasset('images/mascot/bud_cheering.png') }}";
    var BUD_IMG_CELEBRATE = "{{ vasset('images/mascot/bud_celebrate.png') }}";
    var BUD_IMG_NEUTRAL = "{{ vasset('images/mascot/bud_neutral.png') }}";
    var BUD_IMG_APOLOGETIC = "{{ vasset('images/mascot/bud_apologetic.png') }}";
    var BUD_IMG_SAD = "{{ vasset('images/mascot/bud_sad.png') }}";
    var BUD_IMG_WORRIED = "{{ vasset('images/mascot/bud_worried.png') }}";

    var chatBody = document.getElementById("icEmbeddedChatBody");
    var input = document.getElementById("icEmbeddedInput");
    var sendBtn = document.getElementById("icEmbeddedSendBtn");
    var clearBtn = document.getElementById("icBtnClearEmbeddedChat");
    var suggestionsBox = document.getElementById("icEmbeddedSuggestions");
    var headerAvatar = document.querySelector("#icBudHeaderAvatar img");
    var card = document.getElementById("icEmbeddedAiCard");
    var role = card ? card.dataset.role : "student";
    var history = [];
    var isLoading = false;

    function setHeaderState(src) {
        if (headerAvatar && src) {
            headerAvatar.src = src;
        }
    }

    function escapeHtml(str) {
        if (!str) return "";
        return str
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function formatMarkdown(text) {
        if (!text) return "";
        var html = escapeHtml(text);
        html = html.replace(/\*\*(.*?)\*\*/g, "<strong>$1</strong>");
        html = html.replace(/^\s*-\s+(.*)$/gm, "<li>$1</li>");
        html = html.replace(/(<li>.*<\/li>)/s, "<ul>$1</ul>");
        html = html.replace(/\n\n/g, "</p><p>");
        html = html.replace(/\n/g, "<br>");
        return "<p>" + html + "</p>";
    }

    function scrollToBottom() {
        if (chatBody) {
            setTimeout(function () {
                chatBody.scrollTop = chatBody.scrollHeight;
            }, 60);
        }
    }

    function appendUserBubble(text) {
        if (!chatBody) return;
        var row = document.createElement("div");
        row.className = "ic-embedded-msg user";
        row.innerHTML = '<div class="ic-embedded-msg-content">' + escapeHtml(text) + '</div>';
        chatBody.appendChild(row);
        scrollToBottom();
    }

    function showTypingIndicator() {
        if (!chatBody) return;
        setHeaderState(BUD_IMG_THINKING);

        var ind = document.createElement("div");
        ind.className = "ic-embedded-msg bot ic-embedded-typing";
        ind.id = "icEmbeddedTyping";
        ind.innerHTML = [
            '<div class="ic-embedded-msg-avatar">',
            '    <div class="ic-bud-msg-circle"></div>',
            '    <div class="ic-bud-msg-popout">',
            '        <img src="' + BUD_IMG_THINKING + '" alt="Bud Thinking" class="ic-bud-msg-icon">',
            '    </div>',
            '</div>',
            '<div class="ic-embedded-typing-dots">',
            '    <span class="ic-typing-label">Bud is thinking</span>',
            '    <span></span><span></span><span></span>',
            '</div>'
        ].join("");
        chatBody.appendChild(ind);
        scrollToBottom();
    }

    function removeTypingIndicator() {
        var ind = document.getElementById("icEmbeddedTyping");
        if (ind) ind.remove();
    }

    function appendBotBubble(data) {
        if (!chatBody) return;
        var replyHtml = formatMarkdown(data.reply || "");

        // Determine which expressive pose of Bud to show
        var botAvatar = BUD_IMG_THUMBS_UP;
        if (data.errorAvatar) {
            botAvatar = data.errorAvatar;
        } else if (data.escalate) {
            botAvatar = BUD_IMG_APOLOGETIC;
        } else if (data.actions && data.actions.length > 0) {
            botAvatar = BUD_IMG_POINTING;
        } else if (data.celebrate) {
            botAvatar = BUD_IMG_CELEBRATE;
        } else {
            botAvatar = BUD_IMG_THUMBS_UP;
        }

        // Navigation Action Buttons
        var actionsHtml = "";
        if (data.actions && data.actions.length > 0) {
            actionsHtml = '<div class="ic-embedded-actions">';
            data.actions.forEach(function (act) {
                var targetAttr = act.external ? ' target="_blank" rel="noopener noreferrer"' : '';
                var iconTag = act.icon ? '<i class="' + act.icon + '"></i> ' : '<i class="fas fa-arrow-right"></i> ';
                actionsHtml += '<a href="' + act.url + '" class="ic-embedded-action-btn"' + targetAttr + '>' +
                    iconTag + act.label +
                    ' <i class="fas fa-chevron-right ms-auto"></i></a>';
            });
            actionsHtml += '</div>';
        }

        setHeaderState(botAvatar);

        // Support escalation
        var supportHtml = "";
        if (data.escalate) {
            var fb = (data.support && data.support.facebook) ? data.support.facebook : "https://www.facebook.com/profile.php?id=61593939354633";
            var email = (data.support && data.support.email) ? data.support.email : "internconnect.ojtims@gmail.com";
            supportHtml = [
                '<div class="ic-embedded-support-box">',
                '    <div class="ic-support-note"><i class="fas fa-headset"></i> Contact OJT Coordinator:</div>',
                '    <div class="ic-support-links">',
                '        <a href="' + fb + '" target="_blank" rel="noopener noreferrer" class="ic-support-pill fb"><i class="fab fa-facebook-f"></i> Facebook</a>',
                '        <a href="mailto:' + email + '" class="ic-support-pill email"><i class="fas fa-envelope"></i> Email</a>',
                '    </div>',
                '</div>'
            ].join("");
        }

        var row = document.createElement("div");
        row.className = "ic-embedded-msg bot";
        row.innerHTML = [
            '<div class="ic-embedded-msg-avatar">',
            '    <div class="ic-bud-msg-circle"></div>',
            '    <div class="ic-bud-msg-popout">',
            '        <img src="' + botAvatar + '" alt="Bud" class="ic-bud-msg-icon">',
            '    </div>',
            '</div>',
            '<div class="ic-embedded-msg-content">',
            replyHtml,
            actionsHtml,
            supportHtml,
            '</div>'
        ].join("");

        chatBody.appendChild(row);
        scrollToBottom();
    }

    function sendQuery(text) {
        if (!text || isLoading) return;
        isLoading = true;
        if (sendBtn) sendBtn.disabled = true;

        appendUserBubble(text);
        showTypingIndicator();

        var csrfToken = '{{ csrf_token() }}' || document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

        fetch(ENDPOINT, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "Accept": "application/json"
            },
            body: JSON.stringify({
                message: text,
                role: role,
                history: history.slice(-6)
            })
        })
        .then(function (res) {
            return res.json();
        })
        .then(function (data) {
            isLoading = false;
            if (sendBtn) sendBtn.disabled = false;
            removeTypingIndicator();

            if (data && data.success) {
                appendBotBubble(data);
                history.push({ user: text, bot: data.reply });
            } else {
                appendBotBubble({
                    reply: "I am having trouble answering right now. Please reach out to your OJT Coordinator directly.",
                    escalate: true,
                    errorAvatar: BUD_IMG_WORRIED
                });
            }
        })
        .catch(function (err) {
            isLoading = false;
            if (sendBtn) sendBtn.disabled = false;
            removeTypingIndicator();
            appendBotBubble({
                reply: "Network connection error. You can contact support directly via our Facebook page or Email.",
                escalate: true,
                errorAvatar: BUD_IMG_WORRIED
            });
        });
    }

    var initialSuggestionsHtml = suggestionsBox ? suggestionsBox.outerHTML : '';

    // Bindings: Delegated click handler on card (survives dynamic chat clears)
    if (card) {
        card.addEventListener("click", function (e) {
            var chip = e.target.closest(".ic-embedded-chip");
            if (chip && chip.dataset.query) {
                e.preventDefault();
                sendQuery(chip.dataset.query);
            }
        });
    }

    if (sendBtn) {
        sendBtn.addEventListener("click", function () {
            if (!input) return;
            var val = input.value.trim();
            if (val) {
                input.value = "";
                sendQuery(val);
            }
        });
    }

    if (input) {
        input.addEventListener("keydown", function (e) {
            if (e.key === "Enter") {
                e.preventDefault();
                var val = input.value.trim();
                if (val) {
                    input.value = "";
                    sendQuery(val);
                }
            }
        });
    }

    if (clearBtn) {
        clearBtn.addEventListener("click", function () {
            history = [];
            isLoading = false;
            if (sendBtn) sendBtn.disabled = false;
            removeTypingIndicator();
            setHeaderState(BUD_IMG_HAPPY_WAVE);
            if (chatBody) {
                chatBody.innerHTML = [
                    '<div class="ic-embedded-msg bot">',
                    '    <div class="ic-embedded-msg-avatar">',
                    '        <div class="ic-bud-msg-circle"></div>',
                    '        <div class="ic-bud-msg-popout">',
                    '            <img src="' + BUD_IMG_HAPPY_WAVE + '" alt="Bud" class="ic-bud-msg-icon">',
                    '        </div>',
                    '    </div>',
                    '    <div class="ic-embedded-msg-content">',
                    '        <p>Chat cleared! Hi, my name is <strong>Bud</strong>, your OJT Buddy! 🐾 How can I help you navigate InternConnect today?</p>',
                    '    </div>',
                    '</div>',
                    initialSuggestionsHtml
                ].join("");
                scrollToBottom();
            }
        });
    }
})();
</script>
