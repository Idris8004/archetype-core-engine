<div class="archetype-core-wrapper">
    <div class="archetype-container" id="archetype-psych-app">
        <!-- Input Column -->
        <div class="archetype-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <span class="archetype-label" id="psych-step-indicator" style="margin-bottom:0;">Question 1 of 15</span>
                <span style="font-family: var(--archetype-font-mono); font-size: 0.75rem; color: var(--archetype-tertiary-container); background: rgba(98, 250, 227, 0.1); padding: 2px 8px; border-radius: 12px; border: 1px solid rgba(98, 250, 227, 0.2);">IN PROGRESS</span>
            </div>
            
            <form id="archetype-psych-form">
                <?php
                $questions = [
                    "I actively seek out novel methodologies and unconventional approaches to solve complex problems.",
                    "I naturally take charge in chaotic environments to establish structure.",
                    "I naturally gravitate toward leadership roles when a group lacks a clear direction.",
                    "I prioritize group harmony and consensus over asserting my personal opinions.",
                    "I often anticipate potential failures and stress over negative outcomes before they occur.",
                    "I am naturally drawn to theoretical concepts and abstract ideas.",
                    "I meticulously plan my workflows and rarely deviate from scheduled milestones.",
                    "I feel energized and highly productive when interacting with a large network of colleagues.",
                    "I instinctively mediate conflicts to ensure a cohesive and supportive team environment.",
                    "I frequently experience intense, sudden shifts in my emotional state under high-pressure scenarios.",
                    "I prefer environments that allow for creative exploration over strict, rigid adherence to established procedures.",
                    "I find immense satisfaction in executing tasks with high precision and attention to detail.",
                    "I find that engaging in high-energy, collaborative brainstorming sessions recharges my focus.",
                    "I actively adapt my communication style to accommodate the emotional needs of my peers.",
                    "I find it challenging to compartmentalize work-related anxiety when transitioning to personal time."
                ];
                
                $options = [
                    1 => "Strongly Disagree",
                    2 => "Disagree",
                    3 => "Neutral",
                    4 => "Agree",
                    5 => "Strongly Agree"
                ];

                foreach ($questions as $index => $q_text) :
                    $step = $index + 1;
                    $active_class = $step === 1 ? 'active' : '';
                ?>
                <div class="archetype-quiz-step <?php echo $active_class; ?>" data-step="<?php echo $step; ?>">
                    <h3 class="archetype-title" style="font-size: 1.25rem;"><?php echo esc_html($q_text); ?></h3>
                    <div class="archetype-likert" style="flex-direction: column; align-items: stretch; gap: 1rem; border: none; padding: 0; background: transparent;">
                        <div class="archetype-likert-options" style="display: flex; flex-direction: column; gap: 0.75rem; width: 100%;">
                            <?php foreach ($options as $val => $label) : ?>
                                <input type="radio" name="q<?php echo $step; ?>" id="q<?php echo $step . '-' . $val; ?>" value="<?php echo $val; ?>" class="archetype-radio">
                                <label for="q<?php echo $step . '-' . $val; ?>" class="archetype-radio-label" style="width: 100%; padding: 0.75rem 1rem; border-radius: 8px; text-align: center; cursor: pointer;"><?php echo $label; ?></label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <div style="display: flex; justify-content: flex-end; margin-top: 2rem;">
                    <button type="button" class="archetype-button" id="psych_next" style="width: auto; padding: 0.5rem 2rem;">
                        Next <span class="material-symbols-outlined">arrow_forward</span>
                    </button>
                    <button type="submit" class="archetype-button" id="psych_submit" style="display: none; width: auto; padding: 0.5rem 2rem;">
                        <span class="material-symbols-outlined">analytics</span> Analyze
                    </button>
                </div>
            </form>
        </div>

        <!-- Output Column -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div class="archetype-card">
                <canvas id="psychRadarChart"></canvas>
            </div>
            
            <div class="archetype-card">
                <div class="archetype-synthesis-title">
                    <span class="material-symbols-outlined">bar_chart</span> AI Synthesis
                </div>
                <div class="archetype-synthesis-text" id="psych_synthesis_output">
                    Awaiting full data matrix for definitive archetype formulation.
                </div>
            </div>
        </div>
    </div>
</div>
