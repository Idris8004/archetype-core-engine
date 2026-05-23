jQuery(document).ready(function($) {
    
    /**
     * 1. LocalStorage DOB Sync
     */
    const dobInputs = $('.archetype-dob-sync');
    const storedDob = localStorage.getItem('archetype_dob');
    
    if (storedDob) {
        dobInputs.val(storedDob);
    }

    dobInputs.on('change', function() {
        const val = $(this).val();
        localStorage.setItem('archetype_dob', val);
        dobInputs.val(val); // Sync others on page
    });

    /**
     * Helper: AJAX Call to Backend
     */
    function fetchSynthesis(type, dataString, outputElementId) {
        const outputEl = $('#' + outputElementId);
        outputEl.html('<span class="material-symbols-outlined spinner" style="font-size:16px; vertical-align:middle;">sync</span> Synthesizing data matrix...');
        outputEl.parent().css('border-left-color', '#bec6e0'); // pending color

        $.ajax({
            url: archetypeCoreVars.ajax_url,
            type: 'POST',
            data: {
                action: 'archetype_generate_synthesis',
                nonce: archetypeCoreVars.nonce,
                type: type,
                data: dataString
            },
            success: function(response) {
                if(response.success) {
                    outputEl.html(response.data);
                    outputEl.parent().css('border-left-color', 'var(--archetype-tertiary-container)'); // success color
                } else {
                    outputEl.html('<span style="color:var(--archetype-error-container);">Error: ' + response.data + '</span>');
                    outputEl.parent().css('border-left-color', 'var(--archetype-error-container)');
                }
            },
            error: function() {
                outputEl.html('<span style="color:var(--archetype-error-container);">Connection error. Synthesis failed.</span>');
                outputEl.parent().css('border-left-color', 'var(--archetype-error-container)');
            }
        });
    }

    /**
     * 2. Matrix Blueprint Tool
     */
    $('#archetype-matrix-form').on('submit', function(e) {
        e.preventDefault();
        
        const dob = $('#matrix_dob').val();
        const gender = $('#matrix_gender').val();
        
        if(!dob) return;

        // Button state
        const btn = $('#matrix_submit');
        const origText = btn.html();
        btn.html('<span class="material-symbols-outlined spinner">sync</span> Processing...').prop('disabled', true);

        // Placeholder Math Algorithm: Reduce DOB to a number (e.g., base 22 for Tarot/Matrix logic)
        let num = dob.replace(/-/g, '').split('').reduce((acc, val) => acc + parseInt(val), 0);
        while(num > 22) {
            num = num.toString().split('').reduce((acc, val) => acc + parseInt(val), 0);
        }
        
        // Update SVG
        const svgText = $('#matrix-chart-container svg text').first();
        svgText.text(num);
        svgText.css('fill', 'var(--archetype-tertiary-container)');

        // Animate Polygon
        const poly = $('#matrix-chart-container svg polygon');
        poly.css({ 'stroke': 'var(--archetype-primary)', 'opacity': '1', 'transition': 'all 1s' });
        setTimeout(() => poly.css({ 'stroke': 'var(--archetype-tertiary-container)', 'opacity': '0.3' }), 1000);

        // Fetch Synthesis
        const dataStr = `DOB: ${dob}, Gender: ${gender}, Matrix Node: ${num}`;
        fetchSynthesis('Matrix Blueprint', dataStr, 'matrix_synthesis_output');

        // Reset button
        setTimeout(() => btn.html(origText).prop('disabled', false), 500);
    });

    /**
     * 3. BaZi Elements Tool
     */
    $('#archetype-bazi-form').on('submit', function(e) {
        e.preventDefault();
        
        const dob = $('#bazi_dob').val();
        const time = $('#bazi_time').val();
        const loc = $('#bazi_location').val();
        
        if(!dob) return;

        const btn = $('#bazi_submit');
        const origText = btn.html();
        btn.html('<span class="material-symbols-outlined spinner">sync</span> Calculating...').prop('disabled', true);

        // Placeholder Math Algorithm based on DOB string length/chars
        // This simulates a dominant element
        let hash = 0;
        for (let i = 0; i < dob.length; i++) hash = dob.charCodeAt(i) + ((hash << 5) - hash);
        
        const elements = ['wood', 'fire', 'earth', 'metal', 'water'];
        const p1 = Math.abs(hash % 60) + 10; 
        const p2 = Math.abs((hash >> 2) % 20);
        const p3 = Math.abs((hash >> 4) % 10);
        const p4 = Math.abs((hash >> 6) % 5);
        const p5 = 100 - (p1 + p2 + p3 + p4);
        
        const values = [p1, p2, p3, p4, p5];
        // Shuffle based on time to make it look dynamic if time is provided
        if(time) values.reverse();

        let maxIdx = 0;
        let maxVal = 0;

        for(let i=0; i<5; i++) {
            if(values[i] > maxVal) { maxVal = values[i]; maxIdx = i; }
            $('#bazi-val-' + elements[i]).text(values[i] + '%');
            // Animate bar
            setTimeout(() => {
                $('#bazi-bar-' + elements[i]).css('width', values[i] + '%');
            }, 100);
        }

        // Update Dominant Badge
        const domElement = elements[maxIdx].charAt(0).toUpperCase() + elements[maxIdx].slice(1);
        $('#bazi-dominant-badge').html(`<span class="material-symbols-outlined" style="font-size: 14px;">insights</span> <span>${maxVal}% ${domElement} Dominant</span>`);

        // Fetch Synthesis
        const dataStr = `DOB: ${dob}, Time: ${time}, Loc: ${loc}, Wood:${values[0]}%, Fire:${values[1]}%, Earth:${values[2]}%, Metal:${values[3]}%, Water:${values[4]}%`;
        fetchSynthesis('BaZi Elements', dataStr, 'bazi_synthesis_output');

        setTimeout(() => btn.html(origText).prop('disabled', false), 500);
    });

    /**
     * 4. Psych Quiz Tool (OCEAN Radar)
     */
    let psychChart = null;
    let currentStep = 1;
    const totalSteps = 15;

    // Initialize Chart.js Radar if canvas exists
    if($('#psychRadarChart').length > 0 && typeof Chart !== 'undefined') {
        const ctx = document.getElementById('psychRadarChart').getContext('2d');
        psychChart = new Chart(ctx, {
            type: 'radar',
            data: {
                labels: ['O', 'C', 'E', 'A', 'N'],
                datasets: [{
                    label: 'Behavioral Vector',
                    data: [0, 0, 0, 0, 0],
                    backgroundColor: 'rgba(98, 250, 227, 0.2)',
                    borderColor: 'rgba(98, 250, 227, 1)',
                    pointBackgroundColor: 'rgba(98, 250, 227, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    r: {
                        angleLines: { color: 'rgba(255, 255, 255, 0.1)' },
                        grid: { color: 'rgba(255, 255, 255, 0.1)' },
                        pointLabels: { color: '#c4c7c9', font: { family: 'JetBrains Mono', size: 12 } },
                        ticks: { display: false, min: 0, max: 5 }
                    }
                },
                plugins: { legend: { display: false } },
                maintainAspectRatio: false
            }
        });
    }

    $('#psych_next').on('click', function() {
        // Validate current step
        if(!$(`input[name="q${currentStep}"]:checked`).val()) {
            alert("Please select an answer before proceeding.");
            return;
        }

        // Hide current, show next
        $(`.archetype-quiz-step[data-step="${currentStep}"]`).removeClass('active');
        currentStep++;
        
        if(currentStep <= totalSteps) {
            $(`.archetype-quiz-step[data-step="${currentStep}"]`).addClass('active');
            $('#psych-step-indicator').text(`Question ${currentStep} of ${totalSteps}`);
        }
        
        // If last step, swap buttons
        if(currentStep === totalSteps) {
            $('#psych_next').hide();
            $('#psych_submit').show();
        }

        // Live update chart if possible (just visual feedback)
        if(psychChart) {
            let traitIndex = (currentStep - 2) % 5;
            let sum = 0;
            let count = 0;
            for(let i=traitIndex+1; i<=15; i+=5) {
               let qVal = $(`input[name="q${i}"]:checked`).val();
               if(qVal) {
                   sum += parseInt(qVal);
                   count++;
               }
            }
            psychChart.data.datasets[0].data[traitIndex] = count > 0 ? (sum / count) : 0;
            psychChart.update();
        }
    });

    $('#archetype-psych-form').on('submit', function(e) {
        e.preventDefault();
        
        if(!$(`input[name="q${totalSteps}"]:checked`).val()) {
            alert("Please select an answer.");
            return;
        }

        const btn = $('#psych_submit');
        const origText = btn.html();
        btn.html('<span class="material-symbols-outlined spinner">sync</span> Analyzing...').prop('disabled', true);

        // Collect answers
        let answers = [];
        for(let i=1; i<=totalSteps; i++) {
            answers.push( parseInt($(`input[name="q${i}"]:checked`).val()) );
        }

        let O = ((answers[0] + answers[5] + answers[10]) / 3).toFixed(1);
        let C = ((answers[1] + answers[6] + answers[11]) / 3).toFixed(1);
        let E = ((answers[2] + answers[7] + answers[12]) / 3).toFixed(1);
        let A = ((answers[3] + answers[8] + answers[13]) / 3).toFixed(1);
        let N = ((answers[4] + answers[9] + answers[14]) / 3).toFixed(1);

        // Update final chart
        if(psychChart) {
            psychChart.data.datasets[0].data = [O, C, E, A, N];
            psychChart.update();
        }

        $('#psych-step-indicator').text('Analysis Complete');
        $('#psych-step-indicator').next().text('SYNTHESIZED').css({'color':'var(--archetype-primary)', 'background':'var(--archetype-tertiary-container)', 'border-color':'var(--archetype-tertiary-container)'});

        // Fetch Synthesis
        const dataStr = `OCEAN Scores: O:${O}, C:${C}, E:${E}, A:${A}, N:${N}`;
        fetchSynthesis('Psychometric Radar', dataStr, 'psych_synthesis_output');

        setTimeout(() => btn.html(origText).prop('disabled', false), 500);
    });
});
