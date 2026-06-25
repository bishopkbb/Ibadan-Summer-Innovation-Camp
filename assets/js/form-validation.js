/**
 * Ibadan Summer Innovation Camp 2026
 * Client-side form validation — registration, contact, quick-register forms
 */
(function () {
    'use strict';

    var EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    var PHONE_RE = /^[+\d][\d\s\-().]{6,19}$/;

    /* ── human-readable field names ───────────────────────── */
    var LABELS = {
        first_name:             'First name',
        last_name:              'Last name',
        other_name:             'Other name',
        gender:                 'Gender',
        date_of_birth:          'Date of birth',
        age:                    'Age',
        age_group:              'Age group',
        school:                 'Current school',
        class_grade:            'Class / grade',
        address:                'Home address',
        parent_name:            'Parent / guardian name',
        relationship:           'Relationship',
        phone:                  'Phone number',
        alt_phone:              'Alternative phone',
        email:                  'Email address',
        parent_address:         'Residential address',
        learning_track:         'Learning track',
        emergency_contact:      'Emergency contact name',
        emergency_phone:        'Emergency contact phone',
        emergency_relationship: 'Emergency relationship',
        name:                   'Full name',
        subject:                'Subject',
        message:                'Message',
        package:                'Package',
        number_of_children:     'Number of children'
    };

    function label(field) {
        return LABELS[field.name] ||
               (field.placeholder ? field.placeholder.replace(/\s?\*$/, '') : 'This field');
    }

    /* ── per-field state helpers ───────────────────────────── */
    function getGroup(field) {
        return field.closest('.form-group') || field.parentNode;
    }

    function clearState(field) {
        field.classList.remove('v-error', 'v-ok');
        var grp = getGroup(field);
        var old = grp.querySelector('.v-msg[data-field="' + field.name + '"]');
        if (old) old.parentNode.removeChild(old);
    }

    function setError(field, msg) {
        clearState(field);
        field.classList.add('v-error');
        var span = document.createElement('span');
        span.className = 'v-msg';
        span.setAttribute('data-field', field.name);
        span.textContent = msg;
        getGroup(field).appendChild(span);
    }

    function setOk(field) {
        clearState(field);
        field.classList.add('v-ok');
    }

    /* ── single-field validation ───────────────────────────── */
    function validateField(field) {
        var val  = field.value.trim();
        var name = field.name;
        var type = field.type;
        var lbl  = label(field);

        if (field.hasAttribute('required') && !val) {
            setError(field, lbl + ' is required.');
            return false;
        }
        if (!val) { clearState(field); return true; }

        if (type === 'email' || name === 'email') {
            if (!EMAIL_RE.test(val)) {
                setError(field, 'Please enter a valid email address.');
                return false;
            }
        }
        if (type === 'tel' || name === 'phone' || name === 'alt_phone' || name === 'emergency_phone') {
            if (!PHONE_RE.test(val)) {
                setError(field, 'Please enter a valid phone number (e.g. +234 900 000 0000).');
                return false;
            }
        }
        setOk(field);
        return true;
    }

    /* ── required checkboxes (consent) ────────────────────── */
    function validateCheckbox(cb) {
        var lbl   = cb.closest('label') || cb.parentNode;
        var group = lbl.parentNode;
        var key   = 'cb_' + cb.name;
        var old   = group.querySelector('.v-msg[data-field="' + key + '"]');

        if (cb.hasAttribute('required') && !cb.checked) {
            lbl.classList.add('v-cb-error');
            if (!old) {
                var span = document.createElement('span');
                span.className = 'v-msg';
                span.setAttribute('data-field', key);
                span.textContent = 'Your consent is required to proceed.';
                group.appendChild(span);
            }
            return false;
        }
        lbl.classList.remove('v-cb-error');
        if (old) old.parentNode.removeChild(old);
        return true;
    }

    /* ── package radio selection ───────────────────────────── */
    function validatePackage(form) {
        var radios = form.querySelectorAll('input[name="package"]');
        if (!radios.length) return true;
        var checked   = form.querySelector('input[name="package"]:checked');
        var first     = radios[0];
        var section   = first.closest('[style*="border-left"]') || first.closest('.row');
        if (!section) return true;
        var container = section.parentNode || section;
        var old       = container.querySelector('.v-msg[data-field="package_group"]');

        if (!checked) {
            if (!old) {
                var span = document.createElement('p');
                span.className = 'v-msg';
                span.setAttribute('data-field', 'package_group');
                span.style.textAlign = 'center';
                span.textContent = 'Please select a package to continue.';
                container.appendChild(span);
            }
            return false;
        }
        if (old) old.parentNode.removeChild(old);
        return true;
    }

    /* ── course checkbox group ─────────────────────────────── */
    function validateCourses(form) {
        var track = form.querySelector('#learning_track');
        if (!track || !track.value) return true;
        var list    = form.querySelector('#courses-list');
        if (!list) return true;
        var wrapper = list.parentNode;
        var checked = form.querySelectorAll('.course-check:checked');
        var old     = wrapper.querySelector('.v-msg[data-field="courses_group"]');

        if (!checked.length) {
            list.style.borderColor = '#e74c3c';
            if (!old) {
                var span = document.createElement('span');
                span.className = 'v-msg';
                span.setAttribute('data-field', 'courses_group');
                span.textContent = 'Please select at least one course.';
                wrapper.appendChild(span);
            }
            return false;
        }
        list.style.borderColor = '#2ecc71';
        if (old) old.parentNode.removeChild(old);
        return true;
    }

    /* ── full form validation ──────────────────────────────── */
    function validateForm(form) {
        var valid = true;

        /* text / email / tel / date / select / textarea */
        var fields = form.querySelectorAll(
            'input[type="text"], input[type="email"], input[type="tel"],' +
            'input[type="date"], select, textarea'
        );
        fields.forEach(function (f) {
            if (!validateField(f)) valid = false;
        });

        /* required checkboxes */
        form.querySelectorAll('input[type="checkbox"][required]').forEach(function (cb) {
            if (!validateCheckbox(cb)) valid = false;
        });

        /* package radios */
        if (!validatePackage(form)) valid = false;

        /* course checkboxes */
        if (!validateCourses(form)) valid = false;

        return valid;
    }

    /* ── submission error banner ───────────────────────────── */
    function showBanner(form) {
        if (form.querySelector('.v-banner')) return;
        var b = document.createElement('div');
        b.className = 'v-banner';
        b.innerHTML = '<i class="fa-solid fa-triangle-exclamation" style="margin-right:8px;"></i>' +
                      'Please correct the highlighted errors below before submitting.';
        form.insertBefore(b, form.firstChild);
        setTimeout(function () {
            if (b.parentNode) b.parentNode.removeChild(b);
        }, 7000);
    }

    /* ── wire up a form ────────────────────────────────────── */
    function initForm(form) {

        /* live blur + fix-on-type */
        var fields = form.querySelectorAll(
            'input[type="text"], input[type="email"], input[type="tel"],' +
            'input[type="date"], select, textarea'
        );
        fields.forEach(function (f) {
            f.addEventListener('blur',   function () { validateField(f); });
            f.addEventListener('change', function () { validateField(f); });
            f.addEventListener('input',  function () {
                if (f.classList.contains('v-error')) validateField(f);
            });
        });

        /* checkboxes */
        form.querySelectorAll('input[type="checkbox"]').forEach(function (cb) {
            cb.addEventListener('change', function () {
                if (cb.hasAttribute('required')) validateCheckbox(cb);
                if (cb.classList.contains('course-check')) validateCourses(form);
            });
        });

        /* package radios */
        form.querySelectorAll('input[name="package"]').forEach(function (r) {
            r.addEventListener('change', function () { validatePackage(form); });
        });

        /* submit gate */
        form.addEventListener('submit', function (e) {
            if (!validateForm(form)) {
                e.preventDefault();
                showBanner(form);
                var first = form.querySelector('.v-error, .v-cb-error');
                if (first) {
                    first.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    first.focus();
                }
            }
        });
    }

    /* ── auto-init on DOMContentLoaded ────────────────────── */
    document.addEventListener('DOMContentLoaded', function () {
        ['registration-form', 'contact-form', 'quick-registration-form'].forEach(function (id) {
            var form = document.getElementById(id);
            if (form) initForm(form);
        });
    });

}());
