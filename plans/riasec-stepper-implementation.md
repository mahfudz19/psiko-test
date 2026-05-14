# RIASEC Stepper Implementation Plan

## Tanggal

2026-05-14

## Deskripsi

Implementasi Material UI-style stepper untuk Tes RIASEC dengan pengerjaan berurutan per kategori dan error handling untuk pertanyaan yang belum dijawab.

## Requirement Summary

### 1. Stepper Behavior (Material UI Style)

- **Sequential Navigation**: User harus menyelesaikan semua pertanyaan di kategori R dulu sebelum bisa lanjut ke kategori I, dan seterusnya (R → I → A → S → E → C)
- **Active Step Visibility**: Hanya kategori yang sedang aktif yang ditampilkan, kategori lain di-hidden
- **Progress Bar Per Kategori**: Setiap section header memiliki progress bar sendiri yang menunjukkan berapa pertanyaan yang sudah dijawab di kategori tersebut
- **Step Indicator**: Stepper di header menunjukkan kategori mana yang sedang aktif, sudah selesai, atau belum dibuka

### 2. Error Handling (Opsi A)

- **Highlight Error**: Pertanyaan yang belum dijawab akan mendapat border merah saat user mencoba submit
- **Auto Scroll**: Halaman scroll otomatis ke pertanyaan pertama yang belum dijawab
- **Validation Trigger**: Validasi dilakukan saat user klik "Kirim Jawaban" di modal konfirmasi

### 3. UI/UX Theme

- **Global Theme**: Mengikuti design tokens yang sudah ada di Mazu Framework
- **Dimension Colors**:
  - R: `#3B6D11` (Green)
  - I: `#1E5F74` (Teal)
  - A: `#8B5CF6` (Purple)
  - S: `#F59E0B` (Amber)
  - E: `#DC2626` (Red)
  - C: `#6B7280` (Gray)

## Implementation Steps

### Step 1: Update Structure HTML (`take.php`)

#### A. Stepper di Header

Tambahkan stepper component di header yang menunjukkan:

- Total 6 steps (R, I, A, S, E, C)
- Status setiap step: `completed`, `active`, `pending`
- Icon/checkmark untuk step yang sudah selesai

```html
<!-- Stepper Structure (di header) -->
<div class="riasec-stepper">
  <div class="stepper-item completed" data-step="R">
    <span class="stepper-icon">✓</span>
    <span class="stepper-label">Realistic</span>
  </div>
  <div class="stepper-item active" data-step="I">
    <span class="stepper-icon">2</span>
    <span class="stepper-label">Investigative</span>
  </div>
  <div class="stepper-item pending" data-step="A">
    <span class="stepper-icon">3</span>
    <span class="stepper-label">Artistic</span>
  </div>
  <!-- ... dst ... -->
</div>
```

#### B. Dimension Section dengan Progress Bar

Setiap section kategori memiliki progress bar sendiri:

```html
<div class="riasec-dimension-section" data-dimension="R" data-step-index="0">
  <div class="riasec-dimension-header dimension-r">
    <span class="dimension-badge">R</span>
    <h2 class="dimension-title">Realistic</h2>
    <div class="dimension-progress">
      <div class="dimension-progress-bar">
        <div class="dimension-progress-fill" style="width: 0%"></div>
      </div>
      <span class="dimension-progress-text">0/7</span>
    </div>
  </div>

  <div class="riasec-statements">
    <!-- Statements -->
  </div>

  <div class="dimension-navigation">
    <button class="btn btn-prev" disabled>Sebelumnya</button>
    <button class="btn btn-next">Lanjut</button>
  </div>
</div>
```

#### C. Hidden/Active States

Setiap dimension section punya state:

- `active`: Section yang sedang ditampilkan
- `hidden`: Section yang belum/bisa diakses

### Step 2: Update CSS (`take.css`)

#### A. Stepper Styles

```css
.stepper {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.stepper-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  position: relative;
  opacity: 0.5;
  transition: all 0.3s ease;
}

.stepper-item.active {
  opacity: 1;
}

.stepper-item.completed {
  opacity: 1;
}

.stepper-item.completed .stepper-icon {
  background: var(--success-main);
  color: #fff;
}

.stepper-item.active .stepper-icon {
  background: var(--dimension-color);
  color: #fff;
  box-shadow: 0 0 0 3px rgba(..., 0.3);
}

.stepper-icon {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: var(--border-light);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.9rem;
}

.stepper-label {
  font-size: 0.75rem;
  margin-top: 0.25rem;
  text-align: center;
}
```

#### B. Dimension Section States

```css
.riasec-dimension-section {
  display: none; /* Hidden by default */
}

.riasec-dimension-section.active {
  display: block; /* Show only active */
}

.riasec-dimension-section.completed {
  display: none; /* Hide completed sections */
}
```

#### C. Error State Styles

```css
.riasec-statement-item.error {
  border-color: var(--error-main, #dc2626);
  background: var(--error-bg, #fef2f2);
  animation: shake 0.3s ease;
}

.riasec-statement-item.error .statement-options {
  border: 2px solid var(--error-main, #dc2626);
  border-radius: 8px;
  padding: 0.5rem;
}

@keyframes shake {
  0%,
  100% {
    transform: translateX(0);
  }
  25% {
    transform: translateX(-4px);
  }
  75% {
    transform: translateX(4px);
  }
}
```

#### D. Dimension Progress Bar

```css
.dimension-progress {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-left: auto;
}

.dimension-progress-bar {
  width: 100px;
  height: 6px;
  background: var(--border-light);
  border-radius: 3px;
  overflow: hidden;
}

.dimension-progress-fill {
  height: 100%;
  background: linear-gradient(
    90deg,
    var(--dimension-color),
    var(--dimension-color-light)
  );
  transition: width 0.3s ease;
}

.dimension-progress-text {
  font-size: 0.8rem;
  font-weight: 600;
  color: var(--dimension-color);
  min-width: 32px;
}
```

#### E. Navigation Buttons

```css
.dimension-navigation {
  display: flex;
  justify-content: space-between;
  padding: 1rem 1.5rem;
  border-top: 1px solid var(--border-light);
  margin-top: 1rem;
}

.btn-prev:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
```

### Step 3: Update JavaScript (`take.js`)

#### A. State Management

```javascript
const stepperState = {
  currentStep: 0, // Index kategori aktif (0=R, 1=I, 2=A, 3=S, 4=E, 5=C)
  dimensions: ["R", "I", "A", "S", "E", "C"],
  completedSteps: [], // Array index yang sudah selesai
  totalStatements: {}, // Count per kategori
  answeredCount: {}, // Answered count per kategori
};
```

#### B. Stepper Navigation Logic

```javascript
function initStepper() {
  // Initialize stepper state
  // Setup navigation buttons
  // Setup dimension section visibility
}

function goToStep(stepIndex) {
  // Hide all sections
  // Show target section
  // Update stepper UI
  // Update navigation buttons state
  // Scroll to top
}

function nextStep() {
  // Validate current step (all questions answered)
  // If valid, move to next step
  // If invalid, show error
}

function prevStep() {
  // Move to previous step (no validation needed)
}
```

#### C. Validation Logic

```javascript
function validateCurrentStep() {
  const currentDimension = dimensions[stepperState.currentStep];
  const currentSection = document.querySelector(
    `[data-dimension="${currentDimension}"].active`,
  );
  const unansweredItems = currentSection.querySelectorAll(
    ".riasec-statement-item:not(.answered)",
  );

  if (unansweredItems.length > 0) {
    // Mark as error
    unansweredItems.forEach((item) => {
      item.classList.add("error");
    });

    // Scroll to first error
    const firstError = unansweredItems[0];
    firstError.scrollIntoView({ behavior: "smooth", block: "center" });

    return false;
  }

  return true;
}

function clearErrors() {
  document.querySelectorAll(".riasec-statement-item.error").forEach((item) => {
    item.classList.remove("error");
  });
}
```

#### D. Progress Tracking Per Dimension

```javascript
function updateDimensionProgress(dimension) {
  const section = document.querySelector(`[data-dimension="${dimension}"]`);
  const totalStatements = stepperState.totalStatements[dimension];
  const answeredCount = section.querySelectorAll(
    ".riasec-statement-item.answered",
  ).length;
  const progress = (answeredCount / totalStatements) * 100;

  // Update progress bar
  const progressFill = section.querySelector(".dimension-progress-fill");
  const progressText = section.querySelector(".dimension-progress-text");

  progressFill.style.width = progress + "%";
  progressText.textContent = `${answeredCount}/${totalStatements}`;

  // Check if dimension is complete
  if (answeredCount === totalStatements) {
    section.classList.add("completed");
    stepperState.completedSteps.push(
      stepperState.dimensions.indexOf(dimension),
    );
    updateStepperUI();
  }
}

function updateStepperUI() {
  stepperState.dimensions.forEach((dim, index) => {
    const stepperItem = document.querySelector(
      `.stepper-item[data-step="${dim}"]`,
    );

    if (index < stepperState.currentStep) {
      stepperItem.classList.remove("active", "pending");
      stepperItem.classList.add("completed");
      stepperItem.querySelector(".stepper-icon").textContent = "✓";
    } else if (index === stepperState.currentStep) {
      stepperItem.classList.remove("completed", "pending");
      stepperItem.classList.add("active");
      stepperItem.querySelector(".stepper-icon").textContent = index + 1;
    } else {
      stepperItem.classList.remove("active", "completed");
      stepperItem.classList.add("pending");
      stepperItem.querySelector(".stepper-icon").textContent = index + 1;
    }
  });
}
```

#### E. Modified Form Submit Handler

```javascript
function initFormHandler() {
  form.addEventListener("submit", function (e) {
    e.preventDefault();

    // Check if all steps are completed
    if (stepperState.completedSteps.length < stepperState.dimensions.length) {
      // Find first incomplete step
      const firstIncompleteIndex = stepperState.dimensions.findIndex(
        (dim, index) => !stepperState.completedSteps.includes(index),
      );

      // Navigate to that step
      goToStep(firstIncompleteIndex);

      // Show message
      alert(
        "Silakan selesaikan semua pertanyaan pada kategori ini terlebih dahulu.",
      );
      return;
    }

    // All steps completed, show confirmation modal
    confirmModal.showModal();
  });
}
```

### Step 4: Files to Modify

| File       | Changes                                                                                                                                                                                                                                          |
| ---------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| `take.php` | - Tambah stepper component di header<br>- Tambah progress bar di setiap dimension header<br>- Tambah navigation buttons di setiap section<br>- Tambah data attributes untuk stepper logic                                                        |
| `take.css` | - Tambah stepper styles<br>- Tambah dimension section states (active/hidden)<br>- Tambah error state styles<br>- Tambah dimension progress bar styles<br>- Tambah navigation button styles                                                       |
| `take.js`  | - Tambah stepper state management<br>- Tambah stepper navigation logic<br>- Tambah validation logic<br>- Tambah progress tracking per dimension<br>- Modify form submit handler<br>- Modify option click handler untuk update dimension progress |

## Testing Checklist

- [ ] Stepper menunjukkan step yang benar saat pertama load (step 0 = Realistic)
- [ ] Hanya section aktif yang terlihat, section lain hidden
- [ ] Progress bar per kategori update saat user memilih jawaban
- [ ] Navigation button "Lanjut" disabled jika belum semua pertanyaan dijawab
- [ ] Navigation button "Sebelumnya" enabled setelah step > 0
- [ ] Error highlight muncul saat user coba submit dengan pertanyaan kosong
- [ ] Auto scroll ke pertanyaan pertama yang belum dijawab
- [ ] Stepper update status (completed/active/pending) saat navigasi
- [ ] Form bisa submit hanya jika semua 6 kategori selesai
- [ ] Responsive: stepper tetap berfungsi di mobile

## Design Tokens Reference

Mengikuti design tokens dari [`mazu-views/SKILL.md`](.roo/skills/mazu-views/SKILL.md:248-261):

```css
:root {
  --success-main: #3b6d11;
  --success-bg: #f0f9f0;
  --error-main: #dc2626;
  --error-bg: #fef2f2;
  --border-light: #e5e5e5;
  --text-primary: #1a1a1a;
  --text-secondary: #666;
}
```

## Related Files

- [`take.php`](<addon/Views/(app)/tests/riasec/take.php>) - View file
- [`take.css`](<addon/Views/(app)/tests/riasec/take.css>) - Styles
- [`take.js`](<addon/Views/(app)/tests/riasec/take.js>) - JavaScript logic
- [`mazu-views/SKILL.md`](.roo/skills/mazu-views/SKILL.md) - Mazu view standards
