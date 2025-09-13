import { Controller } from "@hotwired/stimulus"

// Connects to data-controller="form-validation"
export default class extends Controller {
  
  static targets = [
    'firstNameInput',
    'firstNameFeedback',
    'lastNameInput',
    'lastNameFeedback',
    'emailInput',
    'emailFeedback',
    'contactNumberInput',
    'contactNumberFeedback',
    'passwordInput',
    'passwordFeedback',
    'passwordConfirmationInput',
    'passwordConfirmationFeedback',
    'formFeedback'
  ];
  connect() {
  }

  validateFirstName(){

    if (!this.hasFirstNameInputTarget || !this.hasFirstNameFeedbackTarget) return

    const input = this.firstNameInputTarget
    const feedback = this.firstNameFeedbackTarget
    const value = input.value.trim()

    const regex = /^[A-Za-z][A-Za-z\s]{2,}$/

    if (value === "") {
      this.setInvalid(input, feedback, "First name is required.")
    } else if (!regex.test(value)) {
      this.setInvalid(
        input,
        feedback,
        "First name must be at least 2 characters and start with a letter."
      )
    } else {
      this.setValid(input, feedback)
    }

    this.updateFormFeedback()
  }

  validateLastName(){
    
    if (!this.hasLastNameInputTarget || !this.hasLastNameFeedbackTarget) return

    const input = this.lastNameInputTarget
    const feedback = this.lastNameFeedbackTarget
    const value = input.value.trim()

    const regex = /^[A-Za-z][A-Za-z\s]{2,}$/

    if (value === "") {
      this.setInvalid(input, feedback, "Last name is required.")
    } else if (!regex.test(value)) {
      this.setInvalid(
        input,
        feedback,
        "Last name must be at least 2 characters and start with a letter."
      )
    } else {
      this.setValid(input, feedback)
    }

    this.updateFormFeedback()
  }

  validateEmail(){

    if (!this.hasEmailInputTarget || !this.hasEmailFeedbackTarget) return

    const input = this.emailInputTarget
    const feedback = this.emailFeedbackTarget
    const value = input.value.trim()

    const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/

    if (value === "") {
      this.setInvalid(input, feedback, "Email is required.")
    } else if (!regex.test(value)) {
      this.setInvalid(input, feedback, "Please enter a valid email address.")
    } else {
      this.setValid(input, feedback)
    }

    this.updateFormFeedback()
  }

  validateContactNumber() {

    if (!this.hasContactNumberInputTarget || !this.hasContactNumberFeedbackTarget) return
    
    const input = this.contactNumberInputTarget
    const feedback = this.contactNumberFeedbackTarget
    const value = input.value.trim()

    // Accepts "09xxxxxxxxx" or "+639xxxxxxxxx"
    const regex = /^(09\d{9}|\+639\d{9})$/

    if (value === "") {
      this.setInvalid(input, feedback, "Contact number is required.")
    } else if (!regex.test(value)) {
      this.setInvalid(
        input,
        feedback,
        "Please enter a valid PH mobile number (e.g., 09171234567 or +639171234567)."
      )
    } else {
      this.setValid(input, feedback)
    }

    this.updateFormFeedback()
  }

  validatePassword() {
    
    if (!this.hasPasswordInputTarget || !this.hasPasswordFeedbackTarget) return

    const input = this.passwordInputTarget
    const feedback = this.passwordFeedbackTarget
    const value = input.value.trim()

    if (value === "") {
      this.setInvalid(input, feedback, "Password is required.")
    } else if (value.length < 8) {
      this.setInvalid(input, feedback, "Password must be at least 8 characters.")
    } else {
      this.setValid(input, feedback)
    }

    // Re-validate confirmation if user edits password
    if (this.hasPasswordConfirmationInputTarget) {
      this.validatePasswordConfirmation()
    }

    this.updateFormFeedback()
  }

  validatePasswordConfirmation() {
    
    if (!this.hasPasswordConfirmatioInputTarget || !this.hasPasswordConfirmatioFeedbackTarget) return

    const input = this.passwordConfirmationInputTarget
    const feedback = this.passwordConfirmationFeedbackTarget
    const password = this.passwordInputTarget.value.trim()
    const confirm = input.value.trim()

    if (confirm === "") {
      this.setInvalid(input, feedback, "Password confirmation is required.")
    } else if (confirm !== password) {
      this.setInvalid(input, feedback, "Passwords do not match.")
    } else {
      this.setValid(input, feedback)
    }

    this.updateFormFeedback()
  }

  validateForm(event) {
    // Run all field validations
    this.validateFirstName()
    this.validateLastName()
    this.validateEmail()
    this.validateContactNumber()
    this.validatePassword()
    this.validatePasswordConfirmation()

    // Check if any input is invalid
    const hasInvalid = this.element.querySelectorAll(".is-invalid").length > 0

    if (hasInvalid) {
      event.preventDefault()
    }
  }

  updateFormFeedback() {
  const hasInvalid = this.element.querySelectorAll(".is-invalid").length > 0

  if (hasInvalid) {
    this.formFeedbackTarget.textContent = "Please correct the errors above."
    this.formFeedbackTarget.style.display = "block"
  } else {
    this.formFeedbackTarget.textContent = ""
    this.formFeedbackTarget.style.display = "none"
  }
}
  setInvalid(input, feedback, message) {
    input.classList.remove("is-valid")
    input.classList.add("is-invalid")
    feedback.textContent = message
  }

  setValid(input, feedback) {
    input.classList.remove("is-invalid")
    input.classList.add("is-valid")
    feedback.textContent = ""
  }
}
