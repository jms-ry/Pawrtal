<template>
  <div class="modal fade" id="rescueRecommendationModal" tabindex="-1" aria-labelledby="rescueRecommendationModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" :class="{ 'modal-lg': step === 'results' }">
      <div class="modal-content">
        <!-- Header -->
        <div class="modal-header bg-primary text-dark fw-bolder">
          <h5 class="modal-title" id="rescueRecommendationModalLabel">
            <i class="bi bi-stars me-2"></i>
            <span v-if="step === 'form'">Find Your Perfect Match</span>
            <span v-else-if="step === 'loading'">Finding Your Matches...</span>
            <span v-else>Your Top Matches</span>
          </h5>
          <button type="button" class="btn-close btn-close-dark fw-bolder" data-bs-dismiss="modal" aria-label="Close" @click="resetModal" :disabled="step === 'loading'"></button>
        </div>

        <!-- STEP 1: Preference Form -->
        <div v-if="step === 'form'" class="modal-body">
          <p class="text-muted mb-4">
            Tell us about your preferences and we'll find the perfect companion for you!
          </p>

          <form @submit.prevent="handleSubmit">
            <!-- Size -->
            <div class="mb-3">
              <label class="form-label fw-bold">Preferred Size</label>
              <select v-model="form.size" class="form-select">
                <option value="small">Small (Chihuahua, Pomeranian)</option>
                <option value="medium">Medium (Beagle, Corgi)</option>
                <option value="large">Large (Labrador, German Shepherd)</option>
                <option value="any">Any Size</option>
              </select>
            </div>

            <!-- Age -->
            <div class="mb-3">
              <label class="form-label fw-bold">Preferred Age</label>
              <select v-model="form.age_preference" class="form-select">
                <option value="puppy">Puppy (0-6 months)</option>
                <option value="young">Young (6 months - 2 years)</option>
                <option value="adult">Adult (2-7 years)</option>
                <option value="senior">Senior (7+ years)</option>
                <option value="any">Any Age</option>
              </select>
            </div>

            <!-- Sex (Radio Buttons - Inline) -->
            <div class="mb-3">
              <label class="form-label fw-bold d-block">Preferred Sex</label>
              <div class="d-flex gap-3">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="sex" id="sexMale" value="male" v-model="form.sex">
                  <label class="form-check-label" for="sexMale">
                    Male
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="sex" id="sexFemale" value="female" v-model="form.sex">
                  <label class="form-check-label" for="sexFemale">
                    Female
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="sex" id="sexAny" value="any" v-model="form.sex">
                  <label class="form-check-label" for="sexAny">
                    No Preference
                  </label>
                </div>
              </div>
            </div>

            <!-- Energy Level -->
            <div class="mb-3">
              <label class="form-label fw-bold">Energy Level</label>
              <select v-model="form.energy_level" class="form-select">
                <option value="low">Low - Calm and relaxed</option>
                <option value="moderate">Moderate - Balanced energy</option>
                <option value="high">High - Very active and playful</option>
                <option value="any">Any Energy Level</option>
              </select>
            </div>

            <!-- Maintenance Level -->
            <div class="mb-3">
              <label class="form-label fw-bold">Maintenance Level</label>
              <select v-model="form.maintenance_level" class="form-select">
                <option value="low">Low - Independent, easy care</option>
                <option value="moderate">Moderate - Regular care needed</option>
                <option value="high">High - Needs lots of attention</option>
                <option value="any">Any Maintenance Level</option>
              </select>
            </div>

            <!-- Temperament -->
            <div class="mb-3">
              <label class="form-label fw-bold">Preferred Temperament (Optional)</label>
              <select v-model="form.temperament" class="form-select">
                <option value="any">Any Temperament</option>
                <option value="friendly">Friendly & Outgoing</option>
                <option value="calm">Calm & Gentle</option>
                <option value="playful">Playful & Fun-loving</option>
                <option value="protective">Protective & Loyal</option>
              </select>
            </div>

            <!-- Error Message -->
            <div v-if="error" class="alert alert-danger" role="alert">
              {{ error }}
            </div>

            <!-- Submit Button -->
            <div class="d-grid">
              <button type="submit" class="btn btn-primary btn-lg" :disabled="loading">
                <span v-if="loading">
                  <span class="spinner-border spinner-border-sm me-2"></span>
                  Finding Your Matches...
                </span>
                <span v-else>
                  <i class="bi bi-search me-2"></i>
                  Find My Perfect Match
                </span>
              </button>
            </div>
          </form>
        </div>

        <!-- STEP 2: Loading Animation -->
        <div v-else-if="step === 'loading'" class="modal-body py-5">
          <div class="text-center mb-4">
            <!-- Animated Icon -->
            <div class="mb-4">
              <i class="bi bi-stars display-1 text-warning loading-pulse"></i>
            </div>

            <!-- Loading Message -->
            <h5 class="mb-4 loading-message">{{ loadingMessage }}</h5>

            <!-- Progress Bar -->
            <div class="progress mb-3" style="height: 30px;">
              <div 
                class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                role="progressbar" 
                :style="{ width: loadingProgress + '%' }"
                :aria-valuenow="loadingProgress" 
                aria-valuemin="0" 
                aria-valuemax="100"
              >
                <span class="fw-bold text-dark">{{ Math.round(loadingProgress) }}%</span>
              </div>
            </div>

            <!-- Fun Facts / Tips (Optional) -->
            <p class="text-muted small mb-0">
              💡 {{ currentFact }}
            </p>
          </div>
        </div>
        <!-- STEP 3: Results Display -->
        <div v-else-if="step === 'results'" class="modal-body">
          <!-- Results Header -->
          <div class="text-center mb-4">
            <h4 class="mb-2">
              <span v-if="matches.length > 0">
                🎉 Found {{ matches.length }} Perfect {{ matches.length === 1 ? 'Match' : 'Matches' }}!
              </span>
              <span v-else>
                😔 No Matches Found
              </span>
            </h4>
            <p v-if="matches.length > 0" class="text-muted mb-0">
              Here are the top {{ matches.length }} rescues that match your preferences
            </p>
            <p v-else class="text-muted mb-0">
              Try adjusting your preferences to find more matches
            </p>
          </div>

          <!-- No Matches State -->
          <div v-if="matches.length === 0" class="text-center py-4">
            <i class="bi bi-emoji-frown display-1 text-muted mb-3"></i>
            <p class="mb-4">
              We couldn't find rescues matching all your criteria right now.
              <br>
              Try different preferences or browse all our rescues!
            </p>
            <div class="d-flex gap-2 justify-content-center">
              <button class="btn btn-primary" @click="backToForm">
                <i class="bi bi-arrow-left me-2"></i>
                Adjust Preferences
              </button>
              <button class="btn btn-secondary" data-bs-dismiss="modal" @click="resetModal" >
                <i class="bi bi-grid me-2"></i>
                Browse All Rescues
              </button>
            </div>
          </div>

          <!-- Matches Grid (Scrollable) -->
          <div v-else class="matches-container mt-3">
            <div v-for="match in matches" :key="match.rescue.id" class="match-card card mt-3 mb-3 shadow-sm">
              <div class="row g-0 h-100">
                <!-- Image -->
                <div class="col-md-4 position-relative">
                  <div class="match-card-image">
                    <img :src="match.rescue.profile_image_url" :alt="match.rescue.name" class="rounded-start">
                    <!-- Match Badge -->
                    <span class="badge bg-success position-absolute top-0 end-0 m-2 fs-6">
                      <i class="bi bi-star-fill me-1"></i>
                      {{ match.match_percentage }}%
                    </span>
                  </div>
                </div>

                <!-- Content -->
                <div class="col-md-8">
                  <div class="card-body">
                    <!-- Name & Basic Info -->
                    <h5 class="card-title mb-2">{{ match.rescue.name }}</h5>
                    <div class="d-flex gap-2 mb-3 flex-wrap">
                      <span class="badge bg-secondary">{{ match.rescue.size }}</span>
                      <span class="badge bg-info">{{ match.rescue.sex }}</span>
                      <span class="badge bg-primary">{{ match.rescue.age }}</span>
                    </div>

                    <!-- Match Reasons -->
                    <div class="mb-3">
                      <h6 class="text-muted mb-2 small">Why this is a great match:</h6>
                      <ul class="list-unstyled mb-0">
                        <li v-for="(reason, index) in match.reasons.slice(0, 3)" :key="index" class="mb-1">
                          <i class="bi bi-check-circle-fill text-success me-2"></i>
                          <small>{{ reason }}</small>
                        </li>
                      </ul>
                    </div>

                    <!-- Actions -->
                    <div class="mt-auto d-flex gap-2">
                      <a :href="`/rescues/${match.rescue.id}`" class="btn btn-sm btn-success" data-bs-dismiss="modal">
                        View Profile
                      </a>
                      <button v-if="!match.rescue.user_has_active_application" class="btn btn-sm btn-primary" @click="openAdoptionModal(match.rescue)">
                        Adopt Me
                      </button>
                      <button v-else class="btn btn-sm btn-secondary" disabled >
                        Already Applied
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Bottom Actions -->
          <div v-if="matches.length > 0" class="mt-4 d-flex gap-2 justify-content-center">
            <button class="btn btn-primary" @click="backToForm">
              <i class="bi bi-arrow-left me-2"></i>
              New Search
            </button>
            <button class="btn btn-secondary" data-bs-dismiss="modal" @click="resetModal">
              <i class="bi bi-grid me-2"></i>
              Browse All
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref } from 'vue'
  import { Modal } from 'bootstrap'

  const step = ref('form') // 'form', 'loading', or 'results'
  const matches = ref([])
  const loading = ref(false)
  const error = ref(null)

  // Loading animation states
  const loadingProgress = ref(0)
  const loadingMessage = ref('')
  const loadingMessages = [
    { text: 'Analyzing your preferences...', duration: 1500 },
    { text: 'Checking household compatibility...', duration: 1500 },
    { text: 'Scanning our rescue database...', duration: 2000 },
    { text: 'Calculating compatibility scores...', duration: 1500 },
    { text: 'Finding your perfect matches...', duration: 1500 },
  ]

  const funFacts = [
    "Did you know? Dogs can understand up to 250 words and gestures!",
    "Fact: A dog's sense of smell is 10,000 to 100,000 times more acute than humans!",
    "Tip: Regular walks and playtime keep your dog happy and healthy!",
    "Fun fact: Dogs dream just like humans do!",
    "Did you know? A dog's nose print is unique, just like human fingerprints!",
  ]

  const currentFact = ref(funFacts[Math.floor(Math.random() * funFacts.length)])

  const form = ref({
    size: 'any',
    age_preference: 'any',
    sex: 'any',
    energy_level: 'any',
    maintenance_level: 'any',
    temperament: 'any',
  })

  const animateLoading = async () => {
    loadingProgress.value = 0
    
    const totalDuration = loadingMessages.reduce((sum, msg) => sum + msg.duration, 0)
    let elapsed = 0
    
    for (const message of loadingMessages) {
      loadingMessage.value = message.text
      
      const steps = 20 // Number of progress updates per message
      const stepDuration = message.duration / steps
      
      for (let i = 0; i < steps; i++) {
        await new Promise(resolve => setTimeout(resolve, stepDuration))
        elapsed += stepDuration
        loadingProgress.value = Math.min(95, (elapsed / totalDuration) * 100)
      }
    }
    
    // Final push to 100%
    loadingProgress.value = 100
  }
  const handleSubmit = async () => {
    loading.value = true
    error.value = null
    step.value = 'loading'

    // Start loading animation
    const loadingAnimation = animateLoading()

    try {
      const response = await fetch('/api/recommendations/match', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify(form.value),
        credentials: 'include',
      })

      if (!response.ok) {
        throw new Error('Failed to fetch recommendations')
      }

      const data = await response.json()

      // Wait for animation to complete
      await loadingAnimation

      if (data.success) {
        // Show top 10 matches only
        matches.value = data.matches.slice(0, 10)

        // Small delay before showing results for dramatic effect
        await new Promise(resolve => setTimeout(resolve, 300))

        step.value = 'results'
      } else {
        error.value = data.message || 'Failed to get recommendations.'
        step.value = 'form'
      }
    } catch (err) {
      error.value = 'An error occurred. Please try again.'
      console.error('Match error:', err)
      step.value = 'form'
    } finally {
      loading.value = false
    }
  }

  const backToForm = () => {
    step.value = 'form'
    error.value = null
    loadingProgress.value = 0
    loadingMessage.value = ''
  }

  const resetModal = () => {
    step.value = 'form'
    matches.value = []
    error.value = null
    loadingProgress.value = 0
    loadingMessage.value = ''
    // Reset form to defaults
    form.value = {
      size: 'any',
      age_preference: 'any',
      sex: 'any',
      energy_level: 'any',
      maintenance_level: 'any',
      temperament: 'any',
    }
  }

  const openAdoptionModal = (rescue) => {
    // Close recommendation modal
    const modal = Modal.getInstance(document.getElementById('rescueRecommendationModal'))
    if (modal) modal.hide()
    
    // Open adoption modal (depends on your existing implementation)
    // You might need to emit an event or use a global state
  }
</script>

<style scoped>
.matches-container {
  max-height: 60vh;
  overflow-y: auto;
}

.match-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  height: 100%;
}

.match-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
}

.match-card .row {
  height: 100%;
}

.match-card-image {
  height: 200px;
  width: 100%;
  overflow: hidden;
  position: relative;
}

.match-card-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
}

.match-card .card-body {
  display: flex;
  flex-direction: column;
  min-height: 200px;
}

.match-card .card-body .mt-auto {
  margin-top: auto !important;
}

/* Loading Animations */
.loading-pulse {
  animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
    transform: scale(1);
  }
  50% {
    opacity: 0.7;
    transform: scale(1.1);
  }
}

.loading-message {
  animation: fadeInOut 1.5s ease-in-out infinite;
  min-height: 30px;
}

@keyframes fadeInOut {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.6;
  }
}

/* Progress bar custom styling */
.progress {
  border-radius: 15px;
  background-color: #e3ecf5;
  box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
}

.progress-bar {
  border-radius: 15px;
  font-size: 14px;
  transition: width 0.3s ease;
}
</style>