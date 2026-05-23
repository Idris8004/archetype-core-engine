<div class="archetype-core-wrapper">
    <div class="archetype-container" id="archetype-bazi-app">
        <!-- Input Column -->
        <div class="archetype-card">
            <h2 class="archetype-title">Baseline Input</h2>
            <form id="archetype-bazi-form">
                <div class="archetype-form-group">
                    <label class="archetype-label" for="bazi_dob">Date of Birth</label>
                    <input type="date" id="bazi_dob" name="dob" class="archetype-input archetype-dob-sync" required>
                </div>
                <div class="archetype-form-group">
                    <label class="archetype-label" for="bazi_time">Time of Birth (Optional)</label>
                    <input type="time" id="bazi_time" name="time" class="archetype-input">
                </div>
                <div class="archetype-form-group">
                    <label class="archetype-label" for="bazi_location">Location of Birth</label>
                    <input type="text" id="bazi_location" name="location" class="archetype-input" placeholder="City, Country">
                </div>
                <button type="submit" class="archetype-button" id="bazi_submit">
                    <span class="material-symbols-outlined">bolt</span> Calculate Elements
                </button>
            </form>
        </div>

        <!-- Output Column -->
        <div class="archetype-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 class="archetype-title" style="margin-bottom:0;">Elemental Distribution</h2>
                <div id="bazi-dominant-badge" style="font-family: var(--archetype-font-mono); font-size: 0.75rem; color: var(--archetype-tertiary-container); display: flex; align-items: center; gap: 4px;">
                    <span class="material-symbols-outlined" style="font-size: 14px;">local_fire_department</span> <span>Awaiting Input</span>
                </div>
            </div>

            <div id="bazi-progress-bars">
                <!-- Wood -->
                <div class="archetype-progress-container">
                    <div class="archetype-progress-header">
                        <span>Wood</span>
                        <span id="bazi-val-wood">0%</span>
                    </div>
                    <div class="archetype-progress-bar-bg">
                        <div class="archetype-progress-bar-fill" id="bazi-bar-wood" style="width: 0%; background: #a7f3d0;"></div>
                    </div>
                </div>
                <!-- Fire -->
                <div class="archetype-progress-container">
                    <div class="archetype-progress-header">
                        <span>Fire</span>
                        <span id="bazi-val-fire">0%</span>
                    </div>
                    <div class="archetype-progress-bar-bg">
                        <div class="archetype-progress-bar-fill" id="bazi-bar-fire" style="width: 0%; background: #fca5a5;"></div>
                    </div>
                </div>
                <!-- Earth -->
                <div class="archetype-progress-container">
                    <div class="archetype-progress-header">
                        <span>Earth</span>
                        <span id="bazi-val-earth">0%</span>
                    </div>
                    <div class="archetype-progress-bar-bg">
                        <div class="archetype-progress-bar-fill" id="bazi-bar-earth" style="width: 0%; background: #fde68a;"></div>
                    </div>
                </div>
                <!-- Metal -->
                <div class="archetype-progress-container">
                    <div class="archetype-progress-header">
                        <span>Metal</span>
                        <span id="bazi-val-metal">0%</span>
                    </div>
                    <div class="archetype-progress-bar-bg">
                        <div class="archetype-progress-bar-fill" id="bazi-bar-metal" style="width: 0%; background: #e5e7eb;"></div>
                    </div>
                </div>
                <!-- Water -->
                <div class="archetype-progress-container">
                    <div class="archetype-progress-header">
                        <span>Water</span>
                        <span id="bazi-val-water">0%</span>
                    </div>
                    <div class="archetype-progress-bar-bg">
                        <div class="archetype-progress-bar-fill" id="bazi-bar-water" style="width: 0%; background: #bfdbfe;"></div>
                    </div>
                </div>
            </div>
            
            <div class="archetype-synthesis-box">
                <div class="archetype-synthesis-title">
                    <span class="material-symbols-outlined">psychology</span> Synthesis Protocol
                </div>
                <div class="archetype-synthesis-text" id="bazi_synthesis_output">
                    Analysis pending. Enter coordinates to initiate the multi-dimensional karmic mapping.
                </div>
            </div>
        </div>
    </div>
</div>
