import os
import argparse
import readline
from datetime import datetime
from openai import OpenAI, APIConnectionError, OpenAIError

# 🔐 Lecture de l'API key
api_key = os.getenv("OPENAI_API_KEY")
if not api_key:
    print("❌ OPENAI_API_KEY n'est pas défini.")
    exit(1)

client = OpenAI(api_key=api_key)

# 🎯 Argument : dossier de travail
parser = argparse.ArgumentParser()
parser.add_argument("workdir", help="Nom du dossier de travail (ex: ecommerce)")
args = parser.parse_args()

# 📁 Définition des chemins
workdir_path = os.path.abspath(args.workdir)
parent_dir = os.getcwd()

if not os.path.isdir(workdir_path):
    print(f"❌ Erreur : dossier '{workdir_path}' introuvable.")
    exit(1)

# 🕒 Timestamp et fichier historique dans le dossier parent
timestamp = datetime.now().strftime("%Y%m%d-%H%M")
history_file = os.path.join(parent_dir, f"codex_history_{timestamp}.txt")

# 📂 On se place dans le dossier de travail
os.chdir(workdir_path)
print(f"📦 Dossier de travail : {workdir_path}")
print(f"📝 Historique sauvegardé dans : {history_file}\n")
print("💬 Shell Codex GPT (tape 'exit' pour quitter)\n")

# 💬 Contexte
messages = [
    {"role": "system", "content": "Tu es un assistant de développement. Réponds de manière claire et concise."}
]

# 🧠 Session interactive
while True:
    try:
        user_input = input(">>> ").strip()
        if user_input.lower() in ["exit", "quit"]:
            print("👋 Session terminée.")
            break

        messages.append({"role": "user", "content": user_input})

        response = client.chat.completions.create(
            model="gpt-4",  # ou gpt-3.5-turbo / gpt-4o
            messages=messages
        )

        reply = response.choices[0].message.content.strip()
        messages.append({"role": "assistant", "content": reply})

        print(reply)

        with open(history_file, "a", encoding="utf-8") as f:
            f.write(f"[USER] {user_input}\n[ASSISTANT] {reply}\n\n")

    except (APIConnectionError, OpenAIError) as e:
        print(f"❌ Erreur API : {e}")
        with open(history_file, "a", encoding="utf-8") as f:
            f.write(f"[ERROR] {str(e)}\n")
    except KeyboardInterrupt:
        print("\n⛔️ Interruption manuelle.")
        break
