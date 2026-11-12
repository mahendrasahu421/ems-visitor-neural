@extends('employee.layout.layout')
@section('title', $title)
@section('main-section')

    <style>
        .progress-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            margin-bottom: 10px;
            position: relative;
            padding: 0 40px;
        }

        .progress-container::before {
            content: "";
            position: absolute;
            top: 20px;
            left: 100px;
            right: 50px;
            height: 3px;
            background: #f16529;
            z-index: 1;
        }

        .step {
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .circle {
            width: 35px;
            height: 35px;
            background: #f16529;
            color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 8px auto;
            font-size: 16px;
        }

        .step p {
            font-size: 14px;
            color: #f16529;
            font-weight: 600;
        }
    </style>
    <x-alert-modal />

    <div class="page-wrapper" style="min-height: 428px;">
        <div class="content">

            <div class="progress-container">
                <div class="step active">
                    <div class="circle"><i class="fas fa-check"></i></div>
                    <p>Self Appraisal</p>
                </div>

                <div class="step active">
                    <div class="circle"><i class="fas fa-check"></i></div>
                    <p>Competencies</p>
                </div>

                <div class="step active">
                    <div class="circle"><i class="fas fa-check"></i></div>
                    <p>KPI</p>
                </div>

                <div class="step active">
                    <div class="circle"><i class="fas fa-check"></i></div>
                    <p>Form-C</p>
                </div>
            </div>

            <!-- Page Header -->
                <h4 class="mb-3">Competency Form / 
                <span class="text-muted">योग्यता प्रपत्र</span></h4>

                
            <!-- Competency Section -->
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Communication</h6>
                    <p class="card-text">
                        Must be able to communicate with everyone in the workplace -- from line staff to executive
                        leadership.
                        Must interact effectively with outsourcing providers, public officials and employees, prospective
                        employees and colleagues. Ability to adapt the communication skills to the audience and the
                        situation.
                        <br><br>
                        कार्यस्थल पर लाइन स्टाफ से लेकर कार्यकारी नेतृत्व तक सभी के साथ संवाद करने में सक्षम होना
                        चाहिए। आउटसोर्सिंग प्रदाताओं, सार्वजनिक अधिकारियों और कर्मचारियों, संभावित कर्मचारियों और सहकर्मियों
                        के
                        साथ प्रभावी ढंग से बातचीत करनी चाहिए। संचार कौशल को दर्शकों और स्थिति के अनुरूप ढालने की क्षमता।
                    </p>

                    <!-- Options -->
                    <div class="row">
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-body">
                                    <input type="radio">
                                    <strong>Option/विकल्प 1</strong>
                                    <p class="mb-0 mt-2">
                                        "Struggle to express ideas clearly and effectively; Have difficulty listening and
                                        understanding
                                        others.
                                        <br>
                                        विचारों को स्पष्ट और प्रभावी ढंग से व्यक्त करने के लिए संघर्ष करना; दूसरों को सुनने
                                        और समझने
                                        में कठिनाई होती है।"
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-body">
                                    <input type="radio">
                                    <strong>Option/विकल्प 2</strong>
                                    <p class="mb-0 mt-2">
                                        "Communicating in a basic manner. Listening and responding to others, but faces
                                        difficulty adjusting communication style to the situation.
                                        <br>
                                        बुनियादी तरीके से संचार करना. दूसरों को सुनना और उनका जवाब देना, लेकिन स्थिति के
                                        अनुसार संचार शैली को समायोजित करने में कठिनाई का सामना करना पड़ता है।"
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-body">
                                    <input type="radio">
                                    <strong>Option/विकल्प 3</strong>
                                    <p class="mb-0 mt-2">
                                        "Communicating clearly and effectively, both written & verbal. Actively listening
                                        and adapting communication style to the audience and situation.
                                        <br>
                                        लिखित और मौखिक दोनों तरह से स्पष्ट और प्रभावी ढंग से संचार करना। दर्शकों और स्थिति
                                        के अनुसार सक्रिय रूप से सुनना और संचार शैली को अपनाना।"
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-body">
                                    <input type="radio">
                                    <strong>Option/विकल्प 4</strong>
                                    <p class="mb-0 mt-2">
                                        "Communicating complex information and ideas clearly and persuasively. Actively
                                        listening, asking thoughtful questions, and providing constructive feedback.
                                        <br>
                                        जटिल जानकारी और विचारों को स्पष्ट और प्रेरक ढंग से संप्रेषित करना। सक्रिय रूप से
                                        सुनना, विचारशील प्रश्न पूछना और रचनात्मक प्रतिक्रिया प्रदान करना।"
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-body">
                                    <input type="radio">
                                    <strong>Option/विकल्प 5</strong>
                                    <p class="mb-0 mt-2">
                                        "Skillfully communicating complex information and ideas, tailoring messages for the
                                        audience. Excellent at active listening, empathizing, and fostering open dialogue.
                                        <br>
                                        जटिल जानकारी और विचारों को कुशलता से संप्रेषित करना, दर्शकों के लिए संदेश तैयार
                                        करना। सक्रिय रूप से सुनने, सहानुभूति व्यक्त करने और खुले संवाद को बढ़ावा देने में
                                        उत्कृष्ट।"
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-body">
                                    <input type="radio">
                                    <strong>Option/विकल्प 6</strong>
                                    <p class="mb-0 mt-2">
                                        "Exceptional communication skills, able to effectively communicate at all levels of
                                        the organization and with diverse stakeholders. Recognized as a trusted advisor and
                                        mentor in communication.
                                        <br>
                                        असाधारण संचार कौशल, संगठन के सभी स्तरों पर और विविध हितधारकों के साथ प्रभावी ढंग से
                                        संवाद करने में सक्षम। संचार में एक विश्वसनीय सलाहकार और संरक्षक के रूप में मान्यता
                                        प्राप्त।"
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Leadership</h6>
                    <p class="card-text">
                        Displaying leadership skill in creating strategic plans for the respective department as well as the
                        overall Employees. Setting up Goals for department and devising ways to achieve them.
                        <br>
                        संबंधित विभाग के साथ-साथ समग्र कार्यबल के लिए रणनीतिक योजनाएँ बनाने में नेतृत्व कौशल प्रदर्शित करना।
                        विभाग के लिए लक्ष्य निर्धारित करना और उन्हें प्राप्त करने के तरीके तैयार करना।
                    </p>

                    <!-- Options -->
                    <div class="row">
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-body">
                                    <input type="radio">
                                    <strong>Option/विकल्प 1</strong>
                                    <p class="mb-0 mt-2">
                                        "Struggle to take initiative or inspire others. Lacking the ability to set clear
                                        goals or motivate a team.
                                        <br>
                                        पहल करने या दूसरों को प्रेरित करने के लिए संघर्ष करें। स्पष्ट लक्ष्य निर्धारित करने
                                        या किसी टीम को प्रेरित करने की क्षमता का अभाव।"
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-body">
                                    <input type="radio">
                                    <strong>Option/विकल्प 2</strong>
                                    <p class="mb-0 mt-2">
                                        "Demonstrate some leadership skills, but rely heavily on others. Able to set basic
                                        goals and provide direction, but lacking the ability to motivate and empower a team.
                                        <br>
                                        कुछ नेतृत्व कौशल प्रदर्शित करें, लेकिन दूसरों पर बहुत अधिक भरोसा करें। बुनियादी
                                        लक्ष्य निर्धारित करने और दिशा प्रदान करने में सक्षम, लेकिन टीम को प्रेरित करने और
                                        सशक्त बनाने की क्षमता का अभाव है।"
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-body">
                                    <input type="radio">
                                    <strong>Option/विकल्प 3</strong>
                                    <p class="mb-0 mt-2">
                                        "Effectively lead and inspire others. Able to set ambitious goals, delegate
                                        responsibilities, and empower team members to achieve exceptional results.
                                        <br>
                                        प्रभावी ढंग से दूसरों का नेतृत्व करें और उन्हें प्रेरित करें। असाधारण परिणाम प्राप्त
                                        करने के लिए महत्वाकांक्षी लक्ष्य निर्धारित करने, जिम्मेदारियाँ सौंपने और टीम के
                                        सदस्यों को सशक्त बनाने में सक्षम।"
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-body">
                                    <input type="radio">
                                    <strong>Option/विकल्प 4</strong>
                                    <p class="mb-0 mt-2">
                                        "Exceptional leadership skills, able to inspire and guide teams to accomplish
                                        ambitious goals. Recognized as a strategic thinker and skilled at developing and
                                        empowering others.
                                        <br>
                                        असाधारण नेतृत्व कौशल, महत्वाकांक्षी लक्ष्यों को पूरा करने के लिए टीमों को प्रेरित
                                        करने और मार्गदर्शन करने में सक्षम। एक रणनीतिक विचारक के रूप में पहचाने जाने वाले और
                                        दूसरों को विकसित करने और सशक्त बनाने में कुशल।"
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-body">
                                    <input type="radio">
                                    <strong>Option/विकल्प 5</strong>
                                    <p class="mb-0 mt-2">
                                        "Skillfully communicating complex information and ideas, tailoring messages for the
                                        audience. Excellent at active listening, empathizing, and fostering open dialogue.
                                        <br>
                                        जटिल जानकारी और विचारों को कुशलता से संप्रेषित करना, दर्शकों के लिए संदेश तैयार
                                        करना। सक्रिय रूप से सुनने, सहानुभूति व्यक्त करने और खुले संवाद को बढ़ावा देने में
                                        उत्कृष्ट।"
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-body">
                                    <input type="radio">
                                    <strong>Option/विकल्प 6</strong>
                                    <p class="mb-0 mt-2">
                                        "Visionary leader who consistently inspires and empowers teams to achieve
                                        extraordinary results. Recognized as a trusted and respected leader within the
                                        organization and industry.
                                        <br>
                                        दूरदर्शी नेता जो असाधारण परिणाम प्राप्त करने के लिए टीमों को लगातार प्रेरित और सशक्त
                                        बनाता है। संगठन और उद्योग के भीतर एक विश्वसनीय और सम्मानित नेता के रूप में पहचाने
                                        जाते हैं।"
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Emotional Intelligence</h6>
                    <p class="card-text">
                        "Self-awareness – the ability to know one's emotions, strengths, weaknesses, drives, values and
                        goals and recognize their impact on others. Self-regulation – involves controlling or redirecting
                        one's disruptive emotions and impulses and adapting to changing circumstances. Social skill –
                        building rapport with others to move them in desired directions. Empathy – considering other
                        people's feelings especially when making decisions Motivation – being driven to achieve for the sake
                        of achievement.
                        <br>
                        आत्म-जागरूकता - किसी की भावनाओं, शक्तियों, कमजोरियों, प्रेरणाओं, मूल्यों और लक्ष्यों को जानने और
                        दूसरों पर उनके प्रभाव को पहचानने की क्षमता। स्व-नियमन - इसमें किसी की विघटनकारी भावनाओं और आवेगों को
                        नियंत्रित करना या पुनर्निर्देशित करना और बदलती परिस्थितियों को अपनाना शामिल है। सामाजिक कौशल -
                        दूसरों को वांछित दिशाओं में ले जाने के लिए उनके साथ संबंध बनाना। सहानुभूति - अन्य लोगों की भावनाओं
                        पर विचार करना, विशेषकर निर्णय लेते समय प्रेरणा - उपलब्धि के लिए उपलब्धि हासिल करने के लिए प्रेरित
                        होना।"
                    </p>

                    <!-- Options -->
                    <div class="row">
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-body">
                                    <input type="radio">
                                    <strong>Option/विकल्प 1</strong>
                                    <p class="mb-0 mt-2">
                                        "Struggle to recognize and manage own emotions, and have difficulty empathizing with
                                        others.
                                        <br>
                                        अपनी भावनाओं को पहचानने और प्रबंधित करने में कठिनाई होती है, और दूसरों के साथ
                                        सहानुभूति रखने में कठिनाई होती है।"
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-body">
                                    <input type="radio">
                                    <strong>Option/विकल्प 2</strong>
                                    <p class="mb-0 mt-2">
                                        "Able to demonstrate a basic understanding of emotions and their impact on behavior,
                                        but have limited ability to manage emotions or empathize with others.
                                        <br>
                                        भावनाओं और व्यवहार पर उनके प्रभाव की बुनियादी समझ प्रदर्शित करने में सक्षम, लेकिन
                                        भावनाओं को प्रबंधित करने या दूसरों के साथ सहानुभूति रखने की क्षमता सीमित है।"
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-body">
                                    <input type="radio">
                                    <strong>Option/विकल्प 3</strong>
                                    <p class="mb-0 mt-2">
                                        "Able to recognize and manage own emotions, and demonstrates empathy and
                                        understanding towards others.
                                        <br>
                                        अपनी भावनाओं को पहचानने और प्रबंधित करने में सक्षम, और दूसरों के प्रति सहानुभूति और
                                        समझ प्रदर्शित करता है।"
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-body">
                                    <input type="radio">
                                    <strong>Option/विकल्प 4</strong>
                                    <p class="mb-0 mt-2">
                                        "Highly attuned to own and others' emotions, and skilled at using emotional
                                        information to navigate social situations and build strong relationships.
                                        <br>
                                        अपनी और दूसरों की भावनाओं के प्रति अत्यधिक अभ्यस्त, और सामाजिक स्थितियों से निपटने
                                        और मजबूत रिश्ते बनाने के लिए भावनात्मक जानकारी का उपयोग करने में कुशल।"
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-body">
                                    <input type="radio">
                                    <strong>Option/विकल्प 5</strong>
                                    <p class="mb-0 mt-2">
                                        "Exceptional emotional intelligence, able to consistently leverage emotional
                                        awareness and understanding to effectively lead and inspire others.
                                        <br>
                                        असाधारण भावनात्मक बुद्धिमत्ता, दूसरों को प्रभावी ढंग से नेतृत्व करने और प्रेरित करने
                                        के लिए लगातार भावनात्मक जागरूकता और समझ का लाभ उठाने में सक्षम।"
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-body">
                                    <input type="radio">
                                    <strong>Option/विकल्प 6</strong>
                                    <p class="mb-0 mt-2">
                                        "Recognized as a role model for emotional intelligence, able to help others develop
                                        and apply these skills to achieve personal and professional growth.
                                        <br>
                                        भावनात्मक बुद्धिमत्ता के लिए एक रोल मॉडल के रूप में मान्यता प्राप्त, व्यक्तिगत और
                                        व्यावसायिक विकास प्राप्त करने के लिए इन कौशलों को विकसित करने और लागू करने में
                                        दूसरों की मदद करने में सक्षम।"
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Relationship Building</h6>
                    <p class="card-text">
                        "Creating a cohesive respective department that works collaboratively to achieve the goals of the
                        department as well as help the organization reach its goals related to Employees development.
                        Relationship-building and interpersonal relationship skills. Establishing credibility of respective
                        Department and Organisation with employees i.e must have the ability to establish credibility and
                        trust as well as balance the obligation to be an advocate for both the organization and its
                        employees.
                        <br>
                        एक सामंजस्यपूर्ण संबंधित विभाग बनाना जो विभाग के लक्ष्यों को प्राप्त करने के लिए सहयोगात्मक रूप से
                        काम करता है और साथ ही संगठन को कार्यबल विकास से संबंधित अपने लक्ष्यों तक पहुंचने में मदद करता है।
                        संबंध-निर्माण और पारस्परिक संबंध कौशल। कर्मचारियों के साथ संबंधित विभाग और संगठन की विश्वसनीयता
                        स्थापित करना यानी विश्वसनीयता और विश्वास स्थापित करने के साथ-साथ संगठन और उसके कर्मचारियों दोनों के
                        लिए एक वकील होने के दायित्व को संतुलित करने की क्षमता होनी चाहिए।"
                    </p>

                    <!-- Options -->
                    <div class="row">
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-body">
                                    <input type="radio">
                                    <strong>Option/विकल्प 1</strong>
                                    <p class="mb-0 mt-2">
                                        "Struggle to build and maintain professional relationships, often comes across as
                                        distant or unapproachable.
                                        <br>
                                        व्यावसायिक संबंध बनाने और बनाए रखने के लिए संघर्ष अक्सर दूर या अप्राप्य के रूप में
                                        सामने आता है।"
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-body">
                                    <input type="radio">
                                    <strong>Option/विकल्प 2</strong>
                                    <p class="mb-0 mt-2">
                                        "Able to establish basic professional relationships, but have difficulty maintaining
                                        them or building trust and rapport.
                                        <br>
                                        बुनियादी व्यावसायिक संबंध स्थापित करने में सक्षम, लेकिन उन्हें बनाए रखने या विश्वास
                                        और तालमेल बनाने में कठिनाई होती है।"
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-body">
                                    <input type="radio">
                                    <strong>Option/विकल्प 3</strong>
                                    <p class="mb-0 mt-2">
                                        "Able to demonstrate the ability to build and maintain professional relationships,
                                        fostering trust and collaboration.
                                        <br>
                                        व्यावसायिक संबंध बनाने और बनाए रखने, विश्वास और सहयोग को बढ़ावा देने की क्षमता
                                        प्रदर्शित करने में सक्षम।"
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-body">
                                    <input type="radio">
                                    <strong>Option/विकल्प 4</strong>
                                    <p class="mb-0 mt-2">
                                        "Skilled at building and nurturing strong professional relationships, adept at
                                        navigating complex social situations and fostering a positive and inclusive work
                                        environment.
                                        <br>
                                        मजबूत व्यावसायिक संबंध बनाने और पोषित करने में कुशल, जटिल सामाजिक परिस्थितियों से
                                        निपटने और सकारात्मक और समावेशी कार्य वातावरण को बढ़ावा देने में कुशल।"
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-body">
                                    <input type="radio">
                                    <strong>Option/विकल्प 5</strong>
                                    <p class="mb-0 mt-2">
                                        "Exceptional relationship-building skills, able to connect with a diverse range of
                                        stakeholders and foster productive and collaborative partnerships.
                                        <br>
                                        असाधारण संबंध-निर्माण कौशल, विभिन्न प्रकार के हितधारकों के साथ जुड़ने और उत्पादक और
                                        सहयोगात्मक साझेदारी को बढ़ावा देने में सक्षम।"
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-body">
                                    <input type="radio">
                                    <strong>Option/विकल्प 6</strong>
                                    <p class="mb-0 mt-2">
                                        "Recognized as a trusted and respected partner, known for one's ability to build and
                                        maintain productive relationships that drive organizational success.
                                        <br>
                                        एक विश्वसनीय और सम्मानित भागीदार के रूप में पहचाना जाता है, जो संगठनात्मक सफलता को
                                        आगे बढ़ाने वाले उत्पादक संबंध बनाने और बनाए रखने की क्षमता के लिए जाना जाता है।"
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Analytical and Critical Thinking</h6>
                    <p class="card-text">
                        "Ability to exercise sound judgment and engage in high-impact decision-making in a number of areas.
                        The ability to analyze situations and view the implications of certain decisions from a critical
                        perspective is essential.
                        <br>
                        कई क्षेत्रों में सही निर्णय लेने और उच्च प्रभाव वाले निर्णय लेने में संलग्न होने की क्षमता।
                        स्थितियों का विश्लेषण करने और कुछ निर्णयों के निहितार्थों को आलोचनात्मक दृष्टिकोण से देखने की क्षमता
                        आवश्यक है।"
                    </p>

                    <!-- Options -->
                    <div class="row">
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-body">
                                    <input type="radio">
                                    <strong>Option/विकल्प 1</strong>
                                    <p class="mb-0 mt-2">
                                        "Struggles to analyze information or make sound decisions.
                                        <br>
                                        जानकारी का विश्लेषण करने या ठोस निर्णय लेने के लिए संघर्ष करना पड़ता है।"
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-body">
                                    <input type="radio">
                                    <strong>Option/विकल्प 2</strong>
                                    <p class="mb-0 mt-2">
                                        "Able to analyze basic information and make simple decisions.
                                        <br>
                                        बुनियादी जानकारी का विश्लेषण करने और सरल निर्णय लेने में सक्षम।"
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-body">
                                    <input type="radio">
                                    <strong>Option/विकल्प 3</strong>
                                    <p class="mb-0 mt-2">
                                        "Analyzes information effectively, identifies relevant data, and develops practical
                                        solutions.
                                        <br>
                                        जानकारी का प्रभावी ढंग से विश्लेषण करता है, प्रासंगिक डेटा की पहचान करता है और
                                        व्यावहारिक समाधान विकसित करता है।"
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-body">
                                    <input type="radio">
                                    <strong>Option/विकल्प 4</strong>
                                    <p class="mb-0 mt-2">
                                        "Excels at analytical and critical thinking, creates innovative solutions to complex
                                        problems.
                                        <br>
                                        विश्लेषणात्मक और आलोचनात्मक सोच में उत्कृष्टता, जटिल समस्याओं के लिए नवीन समाधान
                                        तैयार करता है।"
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-body">
                                    <input type="radio">
                                    <strong>Option/विकल्प 5</strong>
                                    <p class="mb-0 mt-2">
                                        "Recognized as a strategic problem-solver, synthesizes diverse information to drive
                                        transformative change.
                                        <br>
                                        एक रणनीतिक समस्या-समाधानकर्ता के रूप में मान्यता प्राप्त, परिवर्तनकारी परिवर्तन लाने
                                        के लिए विविध सूचनाओं का संश्लेषण करता है।"
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-body">
                                    <input type="radio">
                                    <strong>Option/विकल्प 6</strong>
                                    <p class="mb-0 mt-2">
                                        "Renowned for exceptional analytical abilities and groundbreaking problem-solving
                                        approach.
                                        <br>
                                        असाधारण विश्लेषणात्मक क्षमताओं और अभूतपूर्व समस्या-समाधान दृष्टिकोण के लिए
                                        प्रसिद्ध।"
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5 mb-5">
                <a href="{{ route('employee.dashboard') }}" class="btn btn-primary btn-lg px-4 py-2">
                    Go to Dashboard
                </a>

                <a href="{{ route('employee.kpi.assessment') }}" class="btn btn-primary btn-lg px-4 py-2 me-3">
                    Go to KPI
                </a>
            </div>

        </div>

        <x-footer />
    </div>

@endsection

@push('after_scripts')
    <script>
        // Add JS if needed
    </script>
@endpush
