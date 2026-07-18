(function () {
    'use strict';

    var ROOT_CLASS = 'so-modal-root-v391018683';
    var PANEL_CLASS = 'so-modal-panel-v391018683';
    var PANEL_ONLY_CLASS = 'so-modal-panel-only-v391018683';
    var ANCESTOR_CLASS = 'so-modal-ancestor-reset-v391018683';
    var LOCK_CLASS = 'so-modal-lock-v391018683';
    var armedUntil = 0;
    var locked = false;
    var lockedY = 0;
    var oldBody = null;
    var activeNode = null;
    var activeAncestors = [];

    function trLower(value) {
        return String(value || '').toLocaleLowerCase('tr-TR');
    }

    function isVisible(el) {
        if (!el || !el.isConnected || el.hidden || el.getAttribute('aria-hidden') === 'true') {
            return false;
        }
        var cs = window.getComputedStyle(el);
        if (cs.display === 'none' || cs.visibility === 'hidden' || parseFloat(cs.opacity || '1') === 0) {
            return false;
        }
        var r = el.getBoundingClientRect();
        return r.width > 120 && r.height > 80;
    }

    function hasControls(el) {
        return !!(el && el.querySelector && el.querySelector('input,select,textarea,button,a,[role="button"]'));
    }

    function signature(el) {
        if (!el) return '';
        var id = el.id || '';
        var cls = typeof el.className === 'string' ? el.className : '';
        var role = el.getAttribute ? (el.getAttribute('role') || '') : '';
        var aria = el.getAttribute ? (el.getAttribute('aria-label') || '') : '';
        var text = (el.textContent || '').slice(0, 800);
        return trLower(id + ' ' + cls + ' ' + role + ' ' + aria + ' ' + text);
    }

    function isLikelyModal(el) {
        if (!isVisible(el) || !hasControls(el)) return false;
        var sig = signature(el);
        var cs = window.getComputedStyle(el);
        if (el.tagName === 'DIALOG' || el.getAttribute('role') === 'dialog' || el.getAttribute('aria-modal') === 'true') return true;
        if (sig.indexOf('modal') !== -1 || sig.indexOf('popup') !== -1 || sig.indexOf('dialog') !== -1 || sig.indexOf('overlay') !== -1 || sig.indexOf('sheet') !== -1) return true;
        if (cs.position === 'fixed' && parseInt(cs.zIndex || '0', 10) > 0) return true;
        if (Date.now() < armedUntil && (sig.indexOf('tedarik') !== -1 || sig.indexOf('supplier') !== -1 || sig.indexOf('yeni ekle') !== -1)) return true;
        return false;
    }

    function scoreCandidate(el) {
        var r = el.getBoundingClientRect();
        var sig = signature(el);
        var cs = window.getComputedStyle(el);
        var score = 0;
        score += Math.min((r.width * r.height) / 10000, 300);
        if (el.tagName === 'DIALOG') score += 300;
        if (el.getAttribute('role') === 'dialog' || el.getAttribute('aria-modal') === 'true') score += 260;
        if (sig.indexOf('modal') !== -1 || sig.indexOf('popup') !== -1 || sig.indexOf('dialog') !== -1 || sig.indexOf('overlay') !== -1) score += 200;
        if (sig.indexOf('tedarik') !== -1 || sig.indexOf('supplier') !== -1) score += 180;
        if (cs.position === 'fixed') score += 120;
        if (r.width > window.innerWidth * 0.7 || r.height > window.innerHeight * 0.7) score += 80;
        return score;
    }

    function findCandidate() {
        var selectors = [
            'dialog',
            '[role="dialog"]',
            '[aria-modal="true"]',
            '.modal',
            '.popup',
            '.overlay',
            '.sheet',
            '[class*="modal"]',
            '[class*="popup"]',
            '[class*="dialog"]',
            '[class*="overlay"]',
            '[id*="modal"]',
            '[id*="popup"]',
            '[id*="dialog"]',
            '[id*="supplier"]',
            '[id*="tedarik"]'
        ];
        var nodes = document.querySelectorAll(selectors.join(','));
        var best = null;
        var bestScore = -1;
        for (var i = 0; i < nodes.length; i++) {
            if (!isLikelyModal(nodes[i])) continue;
            var score = scoreCandidate(nodes[i]);
            if (score > bestScore) {
                bestScore = score;
                best = nodes[i];
            }
        }

        if (!best && Date.now() < armedUntil) {
            var all = document.querySelectorAll('body *');
            for (var j = 0; j < all.length; j++) {
                if (!isVisible(all[j]) || !hasControls(all[j])) continue;
                var sig = signature(all[j]);
                if (sig.indexOf('tedarik') === -1 && sig.indexOf('supplier') === -1 && sig.indexOf('yeni ekle') === -1) continue;
                var fallbackScore = scoreCandidate(all[j]);
                if (fallbackScore > bestScore) {
                    bestScore = fallbackScore;
                    best = all[j];
                }
            }
        }
        return best;
    }

    function rootFromCandidate(node) {
        var current = node;
        var best = node;
        var steps = 0;
        while (current && current !== document.body && steps < 8) {
            var sig = signature(current);
            var cs = window.getComputedStyle(current);
            var r = current.getBoundingClientRect();
            if (current.tagName === 'DIALOG' || current.getAttribute('role') === 'dialog' || current.getAttribute('aria-modal') === 'true' || sig.indexOf('modal') !== -1 || sig.indexOf('popup') !== -1 || sig.indexOf('overlay') !== -1 || cs.position === 'fixed' || r.width > window.innerWidth * 0.85 || r.height > window.innerHeight * 0.85) {
                best = current;
            }
            current = current.parentElement;
            steps++;
        }
        return best;
    }

    function choosePanel(root) {
        if (!root || !root.querySelector) return root;
        var explicit = root.querySelector('.modal-dialog,.modal-content,[role="document"],[class*="modal__content"],[class*="popup__content"],[class*="dialog__content"],form');
        if (explicit && explicit !== root) return explicit;
        var children = root.children || [];
        var best = null;
        var bestArea = 0;
        for (var i = 0; i < children.length; i++) {
            if (!isVisible(children[i])) continue;
            var r = children[i].getBoundingClientRect();
            var area = r.width * r.height;
            if (area > bestArea) {
                bestArea = area;
                best = children[i];
            }
        }
        return best || root;
    }

    function clearActive() {
        if (activeNode) {
            activeNode.classList.remove(ROOT_CLASS, PANEL_CLASS, PANEL_ONLY_CLASS);
            var nested = activeNode.querySelectorAll ? activeNode.querySelectorAll('.' + PANEL_CLASS + ',.' + PANEL_ONLY_CLASS) : [];
            for (var i = 0; i < nested.length; i++) {
                nested[i].classList.remove(PANEL_CLASS, PANEL_ONLY_CLASS);
            }
        }
        for (var j = 0; j < activeAncestors.length; j++) {
            activeAncestors[j].classList.remove(ANCESTOR_CLASS);
        }
        activeAncestors = [];
        activeNode = null;
    }

    function lockPage() {
        if (locked) return;
        locked = true;
        lockedY = window.scrollY || window.pageYOffset || 0;
        oldBody = {
            position: document.body.style.position,
            top: document.body.style.top,
            left: document.body.style.left,
            right: document.body.style.right,
            width: document.body.style.width,
            overflow: document.body.style.overflow
        };
        document.documentElement.classList.add(LOCK_CLASS);
        document.body.classList.add(LOCK_CLASS);
        document.body.style.position = 'fixed';
        document.body.style.top = (-lockedY) + 'px';
        document.body.style.left = '0';
        document.body.style.right = '0';
        document.body.style.width = '100%';
        document.body.style.overflow = 'hidden';
    }

    function unlockPage() {
        if (!locked) return;
        locked = false;
        document.documentElement.classList.remove(LOCK_CLASS);
        document.body.classList.remove(LOCK_CLASS);
        document.body.style.position = oldBody ? oldBody.position : '';
        document.body.style.top = oldBody ? oldBody.top : '';
        document.body.style.left = oldBody ? oldBody.left : '';
        document.body.style.right = oldBody ? oldBody.right : '';
        document.body.style.width = oldBody ? oldBody.width : '';
        document.body.style.overflow = oldBody ? oldBody.overflow : '';
        window.scrollTo(0, lockedY);
    }

    function enhance(node) {
        if (!node || !isVisible(node)) return false;
        clearActive();
        var root = rootFromCandidate(node);
        var panel = choosePanel(root);
        var current = root.parentElement;
        while (current && current !== document.body && current !== document.documentElement) {
            current.classList.add(ANCESTOR_CLASS);
            activeAncestors.push(current);
            current = current.parentElement;
        }

        var rr = root.getBoundingClientRect();
        var isOverlay = rr.width > window.innerWidth * 0.72 || rr.height > window.innerHeight * 0.72 || root.tagName === 'DIALOG' || root.getAttribute('role') === 'dialog' || root.getAttribute('aria-modal') === 'true' || signature(root).indexOf('overlay') !== -1;
        if (isOverlay) {
            root.classList.add(ROOT_CLASS);
            if (panel && panel !== root) {
                panel.classList.add(PANEL_CLASS);
            } else {
                root.classList.add(PANEL_CLASS);
            }
        } else {
            root.classList.add(PANEL_ONLY_CLASS);
        }
        activeNode = root;
        lockPage();
        return true;
    }

    function cleanupIfClosed() {
        if (activeNode && isVisible(activeNode)) return;
        clearActive();
        unlockPage();
    }

    function scan() {
        var candidate = findCandidate();
        if (candidate) {
            enhance(candidate);
        } else {
            cleanupIfClosed();
        }
    }

    function arm() {
        armedUntil = Date.now() + 3000;
        window.setTimeout(scan, 0);
        window.setTimeout(scan, 40);
        window.setTimeout(scan, 100);
        window.setTimeout(scan, 220);
        window.setTimeout(scan, 500);
        window.setTimeout(scan, 1000);
        window.setTimeout(scan, 1800);
    }

    document.addEventListener('click', function (event) {
        var target = event.target && event.target.closest ? event.target.closest('button,a,[role="button"],label') : null;
        var text = trLower(target ? ((target.textContent || '') + ' ' + (target.getAttribute('aria-label') || '') + ' ' + (target.id || '') + ' ' + (target.className || '')) : '');
        if (text.indexOf('tedarik') !== -1 || text.indexOf('supplier') !== -1 || text.indexOf('yeni ekle') !== -1) {
            arm();
            return;
        }
        if (text.indexOf('kapat') !== -1 || text.indexOf('vazgeç') !== -1 || text.indexOf('iptal') !== -1 || text === '×' || text === 'x') {
            window.setTimeout(cleanupIfClosed, 80);
            window.setTimeout(cleanupIfClosed, 250);
        }
    }, true);

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            window.setTimeout(cleanupIfClosed, 100);
        }
    });

    var observer = new MutationObserver(function () {
        if (Date.now() < armedUntil || activeNode) {
            window.setTimeout(scan, 0);
            window.setTimeout(cleanupIfClosed, 180);
        }
    });

    function init() {
        observer.observe(document.documentElement, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['class', 'style', 'hidden', 'aria-hidden', 'open']
        });
        scan();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    window.addEventListener('pageshow', scan);
    window.addEventListener('resize', function () {
        if (activeNode) scan();
    });
}());
