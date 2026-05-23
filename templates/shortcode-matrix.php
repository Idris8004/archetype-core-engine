<div class="archetype-core-wrapper">
    <div class="archetype-container" id="archetype-matrix-app">
        <!-- Input Column -->
        <div class="archetype-card">
            <h2 class="archetype-title">Input Coordinates</h2>
            <form id="archetype-matrix-form">
                <div class="archetype-form-group">
                    <label class="archetype-label" for="matrix_dob">Date of Birth</label>
                    <input type="date" id="matrix_dob" name="dob" class="archetype-input archetype-dob-sync" required>
                </div>
                <div class="archetype-form-group">
                    <label class="archetype-label" for="matrix_gender">Gender Matrix</label>
                    <select id="matrix_gender" name="gender" class="archetype-select" required>
                        <option value="male">Male (Yang)</option>
                        <option value="female">Female (Yin)</option>
                    </select>
                </div>
                <button type="submit" class="archetype-button" id="matrix_submit">
                    <span class="material-symbols-outlined">bolt</span> Generate Blueprint
                </button>
            </form>
        </div>

        <!-- Output Column -->
        <div class="archetype-card">
            <div class="archetype-chart-container" id="matrix-chart-container">
                <!-- SVG Octogram injected here -->
                <svg width="200" height="200" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                    <polygon points="100,10 160,40 190,100 160,160 100,190 40,160 10,100 40,40" fill="none" stroke="var(--archetype-tertiary-container)" stroke-width="1" opacity="0.3"/>
                    <circle cx="100" cy="100" r="40" fill="none" stroke="var(--archetype-outline-variant)" stroke-width="1"/>
                    <text x="100" y="105" text-anchor="middle" fill="var(--archetype-primary)" font-family="var(--archetype-font-mono)" font-size="24">??</text>
                    <text x="100" y="195" text-anchor="middle" fill="var(--archetype-on-surface-variant)" font-family="var(--archetype-font-mono)" font-size="10">CORE BLUEPRINT</text>
                </svg>
            </div>
            
            <div class="archetype-synthesis-box">
                <div class="archetype-synthesis-title">
                    <span class="material-symbols-outlined">psychology</span> Synthesis
                </div>
                <div class="archetype-synthesis-text" id="matrix_synthesis_output">
                    Analysis pending. Enter coordinates to initiate the behavioral synthesis protocol.
                </div>
            </div>
        </div>
    </div>
</div>
